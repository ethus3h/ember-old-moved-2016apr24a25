#!/usr/bin/python
# -*- coding: utf-8 -*-
# ARCMAJ3 CLIENT SCRIPT
# Version 2.16, 27 November 2013.
#
# Copyright (C) 2011-2012 WikiTeam
# Arcmaj3 additions copyright 2013 Futuramerlin
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.


# Keys: http://archive.org/account/s3.php
# Documentation: http://archive.org/help/abouts3.txt
# https://wiki.archive.org/twiki/bin/view/Main/IAS3BulkUploader
# http://en.ecgpedia.org/api.php?action=query&meta=siteinfo&siprop=general|rightsinfo&format=xml
#
# TODO: auto updater for appliance
# TODO: Make sure quotation marks are handled correctly by FractureDB
# TODO: install curl in appliance
# TODO: Restructure process: Save URLs. WARC logs. Delete dirs. Have an upload.log for IA, Futuramerlin ULs. Send UL IA URL for URLs.lst to FM, rather than actual URL data.
# TODO: Upload will not be marked as an error uploading bucket if the error is, for example, incorrect authorization keys — cURL always returns success if it sends the request, as far as I can tell. Maybe check its returned data for any XML content?
# TODO: [underway] critical: create a bucket timeout system
# TODO: create an easy way to add projects (e. g. visit http://futuramerlin.com/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=addProject&projectSeed=http://blabla.com/&projectPattern=blabla.com and have it added)
# TODO: create an easy way to add URLs (e. g. visit http://futuramerlin.com/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=addUrl&urlToAdd=http://blabla.com/ and have it added)
# TODO: record megawarc filesizes in barrels table
# TODO: user statistics tracker
# TODO: Make sure odd urls are getting recorded in the database correctly. (e. g. URLs containing ' and such)
# TODO: give up on failed URLs after a certain number of tries
# TODO: bug - upload may (partly) fail if two (small) files are sent to s3 without pause http://p.defau.lt/?puN_G_zKXbv1lz9TfSliPg http://archive.org/details/wiki-editionorg_w or something http://p.defau.lt/?udwrG7YQn4RK_1whl1XWRw http://archive.org/details/wiki-jutedreamhosterscom_lineageii_bestiary
# TODO: minor bug - don't overwrite existing files with same filename in the same identifier
# TODO: fix the addFinished log duplicate issue
# TODO: prevent download duplication — difficult
# [Done] critical: retry failed URLs
# [Done] necessary for final release: config file with username & api keys; include example
# [Done, I think] critical: restrict database submissions to current projects
# [Done] Project statistics tracker
# [Done, I think]: client protocol version indicator

# Configuration goes here
# The optional fourth line of the config file is a comma-separated list of project IDs to crawl.
# You need a file named config.txt with username, access key, and secret key, in two different lines
userName=open('config.txt', 'r').readlines()[0].strip()
accesskey = open('config.txt', 'r').readlines()[1].strip()
secretkey = open('config.txt', 'r').readlines()[2].strip()
try:
    projectsToCrawl = open('config.txt', 'r').readlines()[3].strip()
except Exception, e:
    projectsToCrawl = ''
try:
    NSConfLmDs = open('config.txt', 'r').readlines()[4].strip()
except Exception, e:
    NSConfLmDs = ''
collection = 'opensource' # Replace with "opensource" if you are not an admin of the collection
# end configuration

import os
import re
import subprocess
import gzip
import StringIO
import sys
import time
import urllib
import urllib2
import uuid
import datetime
import string
import random


    # Random User-Agent Generator
    # Version 1.0.0 [edited a bit for this script]
    # Coded by InvisibleMan in Python
    # Download : N/A
    # File     : user.py
    # from: http://pastebin.com/zYPWHnc6
     
uuidG=str(uuid.uuid4())
verd='2'
#GET USER-AGENT
def getUserAgent():
    platform = random.choice(['Macintosh', 'Windows', 'X11'])
    if platform == 'Macintosh':
        os  = random.choice(['68K', 'PPC'])
    elif platform == 'Windows':
        os  = random.choice(['Windows NT 6.0', 'Windows NT 6.1'])
    elif platform == 'X11':
        os  = random.choice(['Linux i686', 'Linux x86_64'])
    browser = random.choice(['chrome', 'firefox', 'ie'])
    if browser == 'chrome':
        webkit = str(random.randint(500, 599))
        version = str(random.randint(0, 24)) + '.0' + str(random.randint(0, 1500)) + '.' + str(random.randint(0, 999))
        return 'Mozilla/5.0 (' + os + ') AppleWebKit/' + webkit + '.0 (KHTML, live Gecko) Chrome/' + version + ' Safari/' + webkit
    elif browser == 'firefox':
        year = str(random.randint(2000, 2012))
        month = random.randint(1, 12)
        if month < 10:
            month = '0' + str(month)
        else:
            month = str(month)
        day = random.randint(1, 30)
        if day < 10:
            day = '0' + str(day)
        else:
            day = str(day)
        gecko = year + month + day
        version = random.choice(['3.6', '5.0', '6.0', '7.0', '8.0', '9.0', '10.0', '11.0', '12.0', '13.0', '14.0', '15.0','17.0','17.0','17.0','18.0','20.0','23.0','24.0','25.0','25.0','25.0'])
        return 'Mozilla/5.0 (' + os + '; rv:' + version + ') Gecko/' + gecko + ' Firefox/' + version
    elif browser == 'ie':
        version = str(random.randint(1, 10)) + '.0'
        engine = str(random.randint(1, 5)) + '.0'
        option = random.choice([True, False])
        if option == True:
            token = random.choice(['.NET CLR', 'SV1', 'Tablet PC', 'Win64; x64', 'WOW64']) + '; '
        elif option == False:
            token = ''
        return 'Mozilla/5.0 (compatible; MSIE ' + version + '; ' + os + '; ' + token + 'Trident/' + engine + ')'
UserAgentChoice=getUserAgent()
#I tried this because of fisheye.toolserver.org seeming to return a PNG for a URL to a browser but HTML to wget, but it doesn't seem to fix the issue. Weird.
#UserAgentChoice="Mozilla/5.0 (X11; Linux i686; rv:25.0) Gecko/20100101 Firefox/25.0"
#from http://stackoverflow.com/questions/35817/how-to-escape-os-system-calls-in-python
def shellesc(s):
    #.replace('&','\\&')
    return s.replace("'", "%27").replace('<','%3C').replace('>','%3E').replace('[','%5B').replace(']','%5D').replace('(','%28').replace(')','%29')

#from https://gist.github.com/edufelipe/1027906



def check_output(*popenargs, **kwargs):
    r"""Run command with arguments and return its output as a byte string.
    
    Backported from Python 2.7 as it's implemented as pure python on stdlib.
    
    >>> check_output(['/usr/bin/python', '--version'])
    Python 2.6.2
    """
    process = subprocess.Popen(stdout=subprocess.PIPE, *popenargs, **kwargs)
    output, unused_err = process.communicate()
    retcode = process.poll()
    if retcode:
        cmd = kwargs.get("args")
        if cmd is None:
            cmd = popenargs[0]
        error = subprocess.CalledProcessError(retcode, cmd)
        error.output = output
        raise error
    return output
now = datetime.datetime.now()


# Nothing to change below
convertlang = {'ar': 'Arabic', 'de': 'German', 'en': 'English', 'es': 'Spanish', 'fr': 'French', 'it': 'Italian', 'ja': 'Japanese', 'nl': 'Dutch', 'pl': 'Polish', 'pt': 'Portuguese', 'ru': 'Russian'}
# This is going to be the barrel data
timeRunning=now.strftime("%Y-%m-%d-%H-%M-%S-%f-%Z_E")
#os.system('bash -c \'wget -O barrelData.txt "http://futuramerlin.com/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=down"\'')
errored = False
def run(command):
    global errored
    commandResult = ''
    try:
        commandRes=check_output(command, shell=True, stderr=subprocess.STDOUT)
        commandResult = "Running command: \n\n" + command + "\n\n\n\n" + commandRes + "\n\n\n\n"
    except Exception, e:
        commandRes=''
        try:
            commandResult = "Running command: \n\n" + command + "\n\n\n\n" + commandResult + str(e.output) + "\n\n\n\nError encountered while running command. This is probably not a big deal.\n\n"
        except Exception, e:
            commandResult = "\n\n\n\nError encountered while running command: \n\n" + command + "\n\n\n\nThis is probably not a big deal. Possibly the command line was incorrectly structured?\n\n"
        errored=True
    return [commandResult,commandRes]
def log_add(text):
    text = str(text)
    print text
    global timeRunning
    f = open('log-'+timeRunning+'.log', 'a')
    f.write(text+"\n")
    f.close()
#vamp='bash -c \'wget -O barrelData.txt "http://futuramerlin.com/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=down"\''
#barrelFetchResult = check_output(vamp, stderr=subprocess.STDOUT, shell=True)
barrelID='NoBarrel'
barrelFetchResult = run('bash -c \'wget -O barrelData.txt --warc-file=AMJ_BarrelData_' + uuidG + "_BarrelList http://futuramerlin.com/d/r/active.php?handler=1\&handlerNeeded=arcmaj3\&amtask=down\&verd="+verd+"\&userName="+userName+"\&projectsToCrawl="+projectsToCrawl+"\&NSConfLmDs="+NSConfLmDs+"\'")
barrelFetchResult=barrelFetchResult[0]
listfile = 'barrelData.txt'
barrelDF = open(listfile, 'r').read().strip().splitlines()
#print 'barrel ID: '+barrelDF[0]
try:
    barrelID=barrelDF[0].strip()
except:
    log_add("Error: Could not retrieve barrel data.\n")
barrelCount=len(barrelDF)-1
def log(wiki, dump, msg):
    global timeRunning
    f = open('uploader-'+timeRunning+'.log', 'a')
    f.write('\n%s;%s;%s' % (wiki, dump, msg))
    f.close()
def addFinished(id):
    global timeRunning
    f = open('barrelsCompleted-'+timeRunning+'.log', 'a')
    f.write(id+"\n")
    f.close()
    #From http://stackoverflow.com/questions/18170647/how-to-delete-duplicate-lines-in-a-text-file-in-unix-bash
    #Doesn't seem to be working :(
    log_add('perl -ne \'print unless $seen{$_}++\' barrelsCompleted-'+timeRunning+'.log')
    os.system('perl -ne \'print unless $seen{$_}++\' barrelsCompleted-'+timeRunning+'.log')
try:
    os.system('rm URLs.lst')
    os.system('rm failed.lst')
except Exception, e:
    log_add("No prior records found\n")
def ulog_add(text):
    text = str(text)
    print text
    global timeRunning
    f = open('upload-'+timeRunning+'.log', 'a')
    f.write(text+"\n")
    f.close()
with open ("barrelData.txt", "r") as barrelDataFile:
    barrelDataContent=barrelDataFile.read()
log_add("\nBarrel fetch output: \n"+barrelFetchResult+"\n\n")
log_add("\nBarrel data: \n"+barrelDataContent+"\n\n")
#print 'bash -c \'wget -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m-\\d-\\H-\\M-\\S-\\6N-\\z\''
#out=os.popen('bash -c \'wget -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m-\\d-\\H-\\M-\\S-\\6N-\\z"\'').read()
#proc = subprocess.Popen(['bash', '-c', '\'wget -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m-\\d-\\H-\\M-\\S-\\6N-\\z\''], stdout=subprocess.PIPE, shell=True)
#(out, err) = proc.communicate()
#log_add("\n\n\n\n\nWget output:"+out+"\n\n\n\n\n")
#log_add("\n\n\n\n\nWget errors:"+error+"\n\n\n\n\n")
timeFetchResult=run('bash -c \'wget -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m-\\d-\\H-\\M-\\S-\\6N-\\z"\'')[0]

with open ("now.txt", "r") as timeFile:
    timeRemote=timeFile.read()
log_add("\Current time fetch output: \n"+timeFetchResult+"\n\n")
log_add("\nCurrent time retrieved remotely: \n"+timeRemote+"\n\n")

def upload(wikis):
    global uuidG
    global errored
    global timeRunning
    global barrelID
    ulog_add(wikis)
    ulog_add("#"*73)
    ulog_add("# Uploading record")
    ulog_add("#"*73)
    dumps = []
    for dirname, dirnames, filenames in os.walk('.'):
        if dirname == '.':
            for f in filenames:
                #log_add('Filenames: ' + str(f))
                if f == 'URLs.lst' or f == 'failed.lst' or f.startswith('bucketsCompleted-') or f.startswith('barrelsCompleted-') or f.startswith('log-') or f.startswith('AMJ_BarrelData_') or f.startswith('AMJ_BucketData_') or f.endswith('.warc.gz') or ('megawarc' in f and (f.endswith('.tar') or f.endswith('.json.gz') or f.endswith('.warc.gz'))):
                    dumps.append(f)
                #if timeRunning in f or timeRemote in f:
                    #dumps.append(f)
            break
    ulog_add(dumps)
    c = 0
    for dump in dumps:
        #if timeRunning in dump or timeRemote in dump:
            #thisRunDataNeeded=true
            #log_add('Current file ('+dump+') is a UUID-less entry for this run.')
        #http://stackoverflow.com/questions/136505/searching-for-uuids-in-text-with-regex
        #dumpid=re.sub(r'_[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}-.{0,5}.warc.gz', '', dump)
        #dumpid=re.sub(r'_combined.warc.gz', '', dump)
        dumpid='AMJ_BarrelData_'+barrelID+'_' + uuidG +'.' +timeRunning
        ulog_add("#"*73)
        ulog_add('ATTEMPTING TO UPLOAD DUMP DATA: ' + dump)
        ulog_add('DUMP ID: ' + dumpid)
        ulog_add("#"*73)
        time.sleep(0.1)


        
        #retrieve some info from the wiki
        wikititle = "Arcmaj3 data. Barrel "+ barrelID + ', ' + uuidG
        wikidesc = "Arcmaj3 data. Barrel "+ barrelID + ', ' + uuidG
        wikikeys = ['Arcmaj3','WARC','snapshot','archive','Arcmaj3BarrelData']                        

        #creates curl command
        curl = ['curl', '--location', 
            '--header', "'x-amz-auto-make-bucket:1'", # Creates the item automatically, need to give some time for the item to correctly be created on archive.org, or everything else will fail, showing "bucket not found" error
            '--header', "'x-archive-queue-derive:0'",
            '--header', "'x-archive-size-hint:%d'" % (os.path.getsize(dump)), 
            '--header', "'authorization: LOW %s:%s'" % (accesskey, secretkey),
        ]
        if c == 0:
            curl += ['--header', "'x-archive-meta-mediatype:web'",
                '--header', "'x-archive-meta-collection:%s'" % (collection),
                '--header', "'x-archive-meta-title:%s'" % (wikititle),
                '--header', "'x-archive-meta-description:%s'" % (wikidesc),
                '--header', "'x-archive-meta-subject:%s'" % ('; '.join(wikikeys)), # Keywords should be separated by ; but it doesn't matter much; the alternative is to set one per field with subject[0], subject[1], ...
                '--header', "'x-archive-meta-mediatype:web'",

            ]
        
        curl += ['--upload-file', "%s" % (dump),
                "http://s3.us.archive.org/" + dumpid + '/' + dump # It could happen that the identifier is taken by another user; only wikiteam collection admins will be able to upload more files to it, curl will fail immediately and get a permissions error by s3.
        ]
        curlline = ' '.join(curl)
        ulog_add('Executing curl request: ')
        ulog_add(curlline+'\n')
        errored = False
        uploadFetchResultB = run(curlline)[0]
        ulog_add('\n\ncurl request result:\n'+uploadFetchResultB+'\n\n')
        c += 1
        #os.system('rm '+"AMJ_BucketData_NoBucket_" + uuid + '*')
        if not (errored or 'XML' in uploadFetchResultB or 'xml' in uploadFetchResultB):
            os.system('rm '+dump)
            ulog_add('Removing file: '+dump+'\n')
        else:
            ulog_add('ERROR UPLOADING BARREL. THIS IS NOT GOOD.')
        errored = False
        ulog_add('Logging added item: ' + 'https://archive.org/details/' + dumpid + '\n\n\n\n\n')
        addFinished(dumpid + '.' + timeRunning)
        #log(dump, 'ok')
#def concat(wikis):
    #global uuidG
    #global errored
    #global timeRunning
    ##log_add(wikis)
    #log_add("\n--\n#"*73)
    #log_add("# CONCATENATING RECORD")
    #log_add("#"*73)
    #dumps = []
    #for dirname, dirnames, filenames in os.walk('.'):
        #if dirname == '.':
            #for f in filenames:
                #if f.startswith('AMJ_BarrelData_') or f.startswith('AMJ_BucketData_') or f.endswith('.warc.gz') or f.contains('megawarc'):
                    #dumps.append(f)
            #break
    #log_add(dumps)
    #for dump in dumps:
        #log_add("\n\nAppending record "+dump+"…\n\n")
        #runCommand='bash -c \'cat '+dump+' >> '+'AMJ_BarrelData_'+barrelID+'_' + uuidG + '_'+'combined.warc.gz;\';'
        #log_add("\n\nRunning command: ")
        #log_add(runCommand)
        #log_add("\n\n")
        #runOutput=run(runCommand)[0]
        #log_add("\n\nCommand output: ")
        #log_add(runOutput)
        #log_add("\n\n")
        #log_add('Removing file: '+dump+'\n')
        #os.system('rm '+dump)
        #log_add("\n\nAppend finished\n\n")
def concatW():
    global errored
    ulog_add("\n--\n"+"#"*73)
    ulog_add("# CONCATENATING RECORDS")
    ulog_add("#"*73)
    errored = False
    cctRes=run('bash -c \'./megawarc pack AMJ_BarrelData_'+barrelID+'_' + uuidG+' *.warc.gz upload-* bucketsCompleted-* barrelsCompleted-* log-* URLs.lst failed.lst *.megawarc.tar *.megawarc.json.gz;\';')
    ulog_add("\n\n"+cctRes[0]+"\n\n")
    ulog_add("\n--\n"+"#"*73)
    ulog_add("# DONE CONCATENATING RECORDS")
    ulog_add("#"*73)
    if not errored:
        ulog_add("Removing records…")
        dumps = []
        for dirname, dirnames, filenames in os.walk('.'):
            if dirname == '.':
                for f in filenames:
                    if (f.startswith('barrelsCompleted-') or f.startswith('bucketsCompleted-') or f.startswith('log-') or f.startswith('AMJ_BarrelData_') or f.startswith('AMJ_BucketData_')) and not 'megawarc' in f:
                        dumps.append(f)
                break
        for dump in dumps:
            ulog_add("\n\n")
            ulog_add('Removing file: '+dump+'\n')
            os.system('rm '+dump)
            ulog_add("\n\nAppend finished\n\n")
    else:
        log_add('ERROR CONCATENATING RECORDS. THIS IS NOT GOOD.')
    errored = False
    return cctRes[0]
log_add('\nPreparing main function\n')

def main():
    global barrelID
    log_add("\nBarrel ID: "+barrelID + "\n")
    log_add('\nEntering main function\n')
    wikis = open(listfile, 'r').read().strip().splitlines()
    #barrelId=wikis[0].strip()
    wikis = wikis[1:]
    global uuidG
    global verd
    global UserAgentChoice
    global userName
    global timeRunning
    global barrelCount
    iId=1
    for wiki in wikis:
        log_add("#"*73)
        log_add('Getting URL ' +str(iId)+ ' of ' +str(barrelCount)+ ': '+wiki+' \n')
        log_add("#"*73+'\n--\n')
        uuid_item=str(uuid.uuid4())
        #[Done, I think] QUOTATION MARKS ARE INCORRECTLY QUOTED
        #initial command: wget -E -K -p -r -l1 --no-parent --warc-tempdir=. --delete-after --user-agent="Mozilla/5.0 (compatible; MSIE 7.0; 68K; WOW64; Trident/2.0)" -e robots=off --warc-max-size=500M --warc-file=AMJ_BarrelData_4148_9f8d370f-aed1-4744-99f5-af860b83eaeb_e8389b55-cb39-40bf-aeb6-f095a3991a78 'http://fisheye.toolserver.org/browse/erfgoed/erfgoedbot/sql/create_view_monuments_all.sql?u=3&r=7.html';
        #in bash wrapper: bash -c 'wget -E -K -p -r -l1 --no-parent --warc-tempdir=. --delete-after --user-agent="Mozilla/5.0 (compatible; MSIE 7.0; 68K; WOW64; Trident/2.0)" -e robots=off --warc-max-size=500M --warc-file=AMJ_BarrelData_4148_9f8d370f-aed1-4744-99f5-af860b83eaeb_e8389b55-cb39-40bf-aeb6-f095a3991a78 '\''http://fisheye.toolserver.org/browse/erfgoed/erfgoedbot/sql/create_view_monuments_all.sql?u=3&r=7.html'\'';'
        #in python: downloadFetchResult=run('bash -c \'wget -E -K -p -r -l1 --no-parent --warc-tempdir=. --delete-after --user-agent="Mozilla/5.0 (compatible; MSIE 7.0; 68K; WOW64; Trident/2.0)" -e robots=off --warc-max-size=500M --warc-file=AMJ_BarrelData_4148_9f8d370f-aed1-4744-99f5-af860b83eaeb_e8389b55-cb39-40bf-aeb6-f095a3991a78 \'\\\'\'http://fisheye.toolserver.org/browse/erfgoed/erfgoedbot/sql/create_view_monuments_all.sql?u=3&r=7.html\'\\\'\';\'')
        downloadFetchResult=run('bash -c \'wget -E -K -p -r -l1 --no-parent --warc-tempdir=. --delete-after --user-agent="'+UserAgentChoice+'" -e robots=off --warc-max-size=500M --warc-file=AMJ_BarrelData_'+barrelID+'_' + uuidG + '_' + uuid_item + ' \'\\\'\''+shellesc(wiki)+'\'\\\'\';\'')
        downloadFetchResult=downloadFetchResult[0]
        logFileName = 'log-'+timeRunning+'.log'
        log_add('\n\nDownload fetch output: \n'+downloadFetchResult+"\n\n")
        iId+=1
    pageLinks=run('bash -c \'grep --binary-files=text "Saving to:" log-'+timeRunning+'.log;\';')[1].replace('Saving to: ‘','')
    pageLinks2='http://'+pageLinks.replace('’','').replace('\n','\nhttp://')
    pageLinks3=run('bash -c \'grep --binary-files=text "^--" log-'+timeRunning+'.log;\';')[1]
    pageLSplit1=pageLinks3.split("\n")
    pageLSplit2=[]
    for entry in pageLSplit1:
        pageLSplit2.append(entry[25:]+"\n")
    pageLinks4=''.join(pageLSplit2)
    #pageLinks4=re.sub("^.{25}",'',pageLinks3)
    pageLinks5=barrelID+'\n'+userName+'\n'+pageLinks2+pageLinks4+'\n'
    log_add('\n\nEXTRACTED LINKS FROM DATA. Link results: \n\n' + pageLinks5 + '\n\n')
    linkGz = StringIO.StringIO()
    gzip_file = gzip.GzipFile(fileobj=linkGz, mode='w')
    gzip_file.write(pageLinks5)
    gzip_file.close()
    f = open('URLs.lst', 'w')
    f.write(linkGz.getvalue())
    f.close()
    logDataRes=open('log-'+timeRunning+'.log', 'r').read()
    logDataList=logDataRes.split("\n--")
    failedUrlsList=[]
    for record in logDataList:
        llRecord=record.split("\n")
        record1l=llRecord[0]
        print 'Record: ' + record
        if '200 OK' not in record:
            failedUrlsList.append(record1l[23:]+"\n")
    failedUrls=''.join(failedUrlsList)
    failedGz = StringIO.StringIO()
    gzip_file = gzip.GzipFile(fileobj=failedGz, mode='w')
    gzip_file.write(failedUrls)
    gzip_file.close()
    f = open('failed.lst', 'w')
    f.write(failedGz.getvalue())
    f.close()
    log_add('\n\nEXTRACTED FAILED URLS FROM DATA. Failed URL list: \n\n' + failedUrls + '\n\n')
    #f = open('linkResults.txt', 'a')
    #f.write(pageLinks5+"\n")
    #f.close()
    #active.php?handler=1&handlerNeeded=arcmaj3&amtask=up
    #curl = ['curl', '--location']
    #curl += [ '--data', '\'handler=1&handlerNeeded=arcmaj3&amtask=up&uploadedBarrelData='+pageLinks5.encode('base64')+'&failedUrlData='+failedUrls.encode('base64')+'\'', '-A', "'"+UserAgentChoice+"'" ]
    #curl += [ "http://futuramerlin.com/d/r/active.php" ]
    #curlline = ' '.join(curl)
    #log_add('Executing curl request: ')
    #log_add(curlline+'\n')
    #Merge available files.
    concatW()
    #Upload megawarc.
    #Upload barrel data back to base.
    ulog_add('\n\nUploading barrel data back to base.\n\n');
    upload(wikis)
    ulog_add('Sleeping 30 seconds to give IA a chance to catch up…')
    time.sleep(30)
    postdata='handler=1&handlerNeeded=arcmaj3&amtask=up'+'&verd='+verd+'&amloc='+'AMJ_BarrelData_'+barrelID+'_' + uuidG +'.' +timeRunning
    ulog_add("\n\nPOST data sent to http://futuramerlin.com/d/r/active.php: \n\n"+postdata+"\n\n(Barrel ID: "+barrelID+")\n\n")
    req = urllib2.Request('http://futuramerlin.com/d/r/active.php', postdata)
    req.add_header('User-agent',UserAgentChoice)
    fp = urllib2.urlopen(req)
    #errored = False
    #uploadBarrelResult = run(curlline)[0]
    #log_add(uploadBarrelResult)
    ulog_add(fp.read())
    #Upload logs?
#if  __name__ == "__main__":
main()

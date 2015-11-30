#!/usr/bin/python
# -*- coding: utf-8 -*-
# ARCMAJ3 CLIENT SCRIPT
# Version 2.18.5, 4 November 2014.
#
# Copyright (C) 2011-2012 WikiTeam
# Arcmaj3 additions copyright 2013, 2014 Futuramerlin
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

# Nuke everything (THINK BEFORE DOING THIS): UPDATE `am_urls` SET failedAttempts=0; UPDATE `am_urls` SET barrel=0;
# Keys: http://archive.org/account/s3.php
# Documentation: http://archive.org/help/abouts3.txt
# https://wiki.archive.org/twiki/bin/view/Main/IAS3BulkUploader
# http://en.ecgpedia.org/api.php?action=query&meta=siteinfo&siprop=general|rightsinfo&format=xml
#
# TODO: Add by-project exclusion patterns (and pattern sets?)
# TODO: user statistics tracker
# TODO: Allow barrels to come in after they've been expired?
# TODO: Use ww2.futuramerlin.com?
# TODO: ACTUALLY use PDO prepared statements to reduce the risk of (intentional or accidental) SQL injection.
# TODO: (?) calculate SHA-512 hashes client-side to reduce DB server load (this would calculate hashes even if the URL was already in the database, though…)
# TODO: [difficult] retain link depth data. Only crawl for a set number of hops?
# TODO: [difficult] Store hash of downloaded URLs' contents in database for future duplicate checking.
# TODO: bug - upload may (partly) fail if two (small) files are sent to s3 without pause http://p.defau.lt/?puN_G_zKXbv1lz9TfSliPg http://archive.org/details/wiki-editionorg_w or something http://p.defau.lt/?udwrG7YQn4RK_1whl1XWRw http://archive.org/details/wiki-jutedreamhosterscom_lineageii_bestiary
# TODO: minor bug - don't overwrite existing files with same filename in the same identifier
# TODO: fix the addFinished log duplicate issue
# [As much done as presently practical]: prevent download duplication — difficult
# [Pretty much done, but not fully automated]: [underway] critical: create a bucket timeout system
# [Done, I think] critical: restrict database submissions to current projects
# [Done, I think]: client protocol version indicator
# [Done, I think]: Make sure quotation marks are handled correctly by FractureDB
# [Done, I think]: give up on failed URLs after a certain number of tries
# [Done, I think]: delete empty directories
# [Done, I think]: Upload will not be marked as an error uploading bucket if the error is, for example, incorrect authorization keys — cURL always returns success if it sends the request, as far as I can tell. Maybe check its returned data for any XML content?
# [Done, I think]: Make sure odd urls are getting recorded in the database correctly. (e. g. URLs containing ' and such)
# [Done, I think]: Fix active.php so that a duplicate entry doesn't rollback the entire set of URLs.
# [Done, I think]: Zip megawarc tar
# [Done, I think]: remove duplicate out/failed URLs client-side to reduce DB server load
# [Done, I think]: Retain origin barrel data for URLs table
# [Done, I think]: Gracefully handle a failure to get a barrel because the server's down
# [Done, I think]: Use LOAD DATA LOCAL INFILE to add the URL discovery data
# [Done] critical: retry failed URLs
# [Done] necessary for final release: config file with username & api keys; include example
# [Done] Project statistics tracker
# [Done]: Restructure process: Save URLs. WARC logs. Have an upload.log for IA, Futuramerlin ULs. Send UL IA URL for URLs.lst to FM, rather than actual URL data.
# [Done]: auto updater for appliance
# [Done]: install curl in appliance
# [Done]: record megawarc filesizes in barrels table
# [Done]: create an easy way to add projects (e. g. visit http://localhost:8888/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=addProject&projectSeed=http://blabla.com/&projectPattern=blabla.com and have it added)
# [Done]: create an easy way to add URLs (e. g. visit http://localhost:8888/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=addUrl&urlToAdd=http://blabla.com/ and have it added)

#help from http://superuser.com/questions/142459/persistent-retrying-resuming-downloads-with-curl

# Configuration goes here
# The optional fourth line of the config file is a comma-separated list of project IDs to crawl.
# You need a file named config.txt with username, access key, and secret key, each in its own line.
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
try:
    HerWebPort = open('config.txt', 'r').readlines()[5].strip()
except Exception, e:
    HerWebPort = '42643'
try:
    #Version might be able to be autodetected by running:
    #  $ openssl version
    #See http://www.madboa.com/geek/openssl/#intro-version
    sslType = open('config.txt', 'r').readlines()[6].strip()
    if sslType == '0':
        sslTypeS = ''
    else:
        sslTypeS = ' --sslv3'
except Exception, e:
    sslTypeS = ''
collection = 'amjbarreldata' # Replace with "opensource" if you are not an admin of the collection
# end configuration

import os
import re
import subprocess
import gzip
import StringIO
import sys
import cgi
import time
import urllib
import urllib2
import uuid
import datetime
import string
import random
from faker import Factory
fake = Factory.create()
global barrelSize
barrelSize=0

    # Random User-Agent Generator
    # Version 1.0.0 [edited a bit for this script]
    # Coded by InvisibleMan in Python
    # Download : N/A
    # File     : user.py
    # from: http://pastebin.com/zYPWHnc6
     
uuidG=str(uuid.uuid4())
verd='2'
# from http://stackoverflow.com/questions/18609778/solved-python3-convert-all-characters-to-html-entities
def htmlEntities( string ):
    return ''.join(['&#{0};'.format(ord(char)) for char in string])
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
#UserAgentChoice=getUserAgent()
altUserAgentChoice=getUserAgent()
UserAgentChoice=fake.user_agent()
#I tried this because of fisheye.toolserver.org seeming to return a PNG for a URL to a browser but HTML to wget, but it doesn't seem to fix the issue. Weird.
#UserAgentChoice="Mozilla/5.0 (X11; Linux i686; rv:25.0) Gecko/20100101 Firefox/25.0"
#from http://stackoverflow.com/questions/35817/how-to-escape-os-system-calls-in-python
def shellesc(s):
    #.replace('&','\\&')
    return s.replace("'", "%27").replace(' ','%20').replace('<','%3C').replace('>','%3E').replace('[','%5B').replace(']','%5D').replace('(','%28').replace(')','%29').replace(';','%3B').replace("\x00",'%00').replace("\x0c",'%0C').replace("\x0b",'%0B').replace("\x08",'%08').replace("\x03",'%03')

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
#os.system('bash -c \'wget -O barrelData.txt "http://localhost:8888/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=down"\'')
errored = False
def run(command):
    print command
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
def hlog_add(text):
    text = str(text)
    print text
    global timeRunning
    f = open('amc_H3_log-'+timeRunning+'.log', 'a')
    f.write(text+"\n")
    f.close()
#vamp='bash -c \'wget -O barrelData.txt "http://localhost:8888/d/r/active.php?handler=1&handlerNeeded=arcmaj3&amtask=down"\''
#barrelFetchResult = check_output(vamp, stderr=subprocess.STDOUT, shell=True)
barrelID='NoBarrel'
errored=False
barrelFetchResult = run('bash -c \'wget --no-check-certificate -O barrelData.txt --warc-file=AMJ_BarrelData_' + uuidG + "_BarrelList http://localhost:8888/d/r/active.php?handler=1\&handlerNeeded=arcmaj3\&amtask=down\&verd="+verd+"\&userName="+userName+"\&projectsToCrawl="+projectsToCrawl+"\&NSConfLmDs="+NSConfLmDs+"\'")
if errored == True:
    log_add('Failed to retrieve barrel data. Sleeping 60 seconds…')
    time.sleep(60)
    log_add('…and quitting.')
    sys.exit()
errored=False
barrelFetchResult=barrelFetchResult[0]
listfile = 'barrelData.txt'
barrelDF = open(listfile, 'r').read().strip().splitlines()
#print 'barrel ID: '+barrelDF[0]
try:
    barrelID=str(int(barrelDF[0].strip()))
except:
    log_add("Error: Could not read barrel data.\n")
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
timeFetchResult=run('bash -c \'wget --no-check-certificate -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m-\\d-\\H-\\M-\\S-\\6N-\\z"\'')[0]

with open ("now.txt", "r") as timeFile:
    timeRemote=timeFile.read()
log_add("\Current time fetch output: \n"+timeFetchResult+"\n\n")
log_add("\nCurrent time retrieved remotely: \n"+timeRemote+"\n\n")
statDuro=False
def upload(wikis):
    global uuidG
    global errored
    global timeRunning
    global barrelID
    global statDuro
    ulog_add(wikis)
    ulog_add("#"*73)
    ulog_add("# Uploading record")
    ulog_add("#"*73)
    dumps = []
    for dirname, dirnames, filenames in os.walk('.'):
        if dirname == '.':
            for f in filenames:
                #log_add('Filenames: ' + str(f))
                if f.endswith('.xz') or f.endswith('.7z') or f == 'URLs.lst' or f == 'failed.lst' or f.startswith('bucketsCompleted-') or f.startswith('barrelsCompleted-') or f.startswith('log-') or f.startswith('AMJ_BarrelData_') or f.startswith('AMJ_BucketData_') or f.endswith('.warc.gz') or ('megawarc' in f and (f.endswith('.tar') or f.endswith('.json.gz') or f.endswith('.warc.gz'))):
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
        global barrelSize
        if os.path.getsize(dump) > barrelSize:
            barrelSize = int(os.path.getsize(dump))
        #creates curl command
        global sslTypeS
        curl = ['curl', '--location', 
        	'--retry', '999',
        	'--retry-max-time', '0',
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
        if not (errored or 'XML' in uploadFetchResultB or 'xml' in uploadFetchResultB or 'html' in uploadFetchResultB or 'HTML' in uploadFetchResultB):
            os.system('rm '+dump)
            ulog_add('Removing file: '+dump+'\n')
            statDuro = True
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
    cctRes=run('bash -c \'./megawarc pack AMJ_BarrelData_'+barrelID+'_' + uuidG+' *.warc.gz *.7z *.xz upload-* bucketsCompleted-* barrelsCompleted-* log-* arcmaj3-client.py URLs.lst failed.lst *.megawarc.tar *.megawarc.json.gz;\';')
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
                    if ((f.startswith('upload-') and not 'upload-'+timeRunning+'.log' in f) or f.startswith('barrelsCompleted-') or f.startswith('bucketsCompleted-') or f.startswith('log-') or f.startswith('AMJ_BarrelData_') or f.startswith('AMJ_BucketData_') or (f.startswith('WEB-') and f.endswith('.warc.gz'))) and not 'megawarc' in f:
                        dumps.append(f)
                    if (f.startswith('ytdlamjoutput_')):
                    	dumps.append(f)
                break
        for dump in dumps:
            ulog_add("\n\n")
            ulog_add('Removing file: '+dump+'\n')
            os.system('rm '+dump)
            ulog_add("\n\nAppend finished\n\n")
    else:
        log_add('ERROR CONCATENATING RECORDS. THIS IS NOT GOOD.')
    ulog_add('\n\nCompressing records…\n\n')
    ulog_add(run('xz AMJ_BarrelData_'+barrelID+'_' + uuidG+'.megawarc.tar')[0])
    ulog_add('\n\nFinished compressing records…\n\n')
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
    global errored
    global uuidG
    global verd
    global UserAgentChoice
    global userName
    global timeRunning
    global barrelCount
    global barrelSize
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
        downloadFetchResult=run('bash -c \'wget --no-check-certificate -E -K --no-parent --warc-tempdir=. --delete-after --user-agent="'+altUserAgentChoice+'" -e robots=off --warc-max-size=500M --warc-file=AMJ_BarrelData_'+barrelID+'_' + uuidG + '_' + uuid_item + ' \'\\\'\''+shellesc(wiki)+'\'\\\'\';\'')
        downloadFetchResult=downloadFetchResult[0]
        logFileName = 'log-'+timeRunning+'.log'
        ytdlResult = run('bash -c \'youtube-dl --restrict-filenames -o ytdlamjoutput_%(autonumber)s_%(playlist)s_%(playlist_index)s_%(id)s.%(ext)s --continue --retries 4 --write-info-json --write-description --write-thumbnail --write-annotations --all-subs --ignore-errors -f 38/138+141/138+22/138+140/138+139/264+141/264+22/264+140/264+139/137+141/137+22/137+140/137+139/37/22/135+141/135+22/135+140/135+139/best \'\\\'\''+shellesc(wiki)+'\'\\\'\';\'')
        log_add('\n\nDownload fetch output: \n'+downloadFetchResult+"\n\n")
        log_add('\n\nyoutube-dl output: \n'+ytdlResult+"\n\n")
        iId+=1
    job_data="""<?xml version="1.0" encoding="UTF-8"?>
<!-- 
  HERITRIX 3 CRAWL JOB CONFIGURATION FILE
  
   This is a relatively minimal configuration suitable for many crawls.
   
   Commented-out beans and properties are provided as an example; values
   shown in comments reflect the actual defaults which are in effect
   if not otherwise specified specification. (To change from the default 
   behavior, uncomment AND alter the shown values.)   
 -->
<beans xmlns="http://www.springframework.org/schema/beans"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:context="http://www.springframework.org/schema/context"
        xmlns:aop="http://www.springframework.org/schema/aop"
        xmlns:tx="http://www.springframework.org/schema/tx"
        xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans-3.0.xsd
           http://www.springframework.org/schema/aop http://www.springframework.org/schema/aop/spring-aop-3.0.xsd
           http://www.springframework.org/schema/tx http://www.springframework.org/schema/tx/spring-tx-3.0.xsd
           http://www.springframework.org/schema/context http://www.springframework.org/schema/context/spring-context-3.0.xsd">
 
 <context:annotation-config/>

 <!-- overrides from a text property list -->
 <bean id="simpleOverrides" class="org.springframework.beans.factory.config.PropertyOverrideConfigurer">
  <property name="properties">
   <value>

metadata.operatorContactUrl="""+fake.url()+"""
metadata.jobName=basic
metadata.description=Basic crawl starting with useful defaults

##..more?..##
   </value>
  </property>
 </bean>

 <!-- overrides from declared <prop> elements, more easily allowing
      multiline values or even declared beans -->
 <bean id="longerOverrides" class="org.springframework.beans.factory.config.PropertyOverrideConfigurer">
  <property name="properties">
   <props>
    <prop key="seeds.textSource.value">

# URLS HERE

"""+cgi.escape("\n".join(wikis),True).encode("ascii", "xmlcharrefreplace").replace("\x00",'%00').replace("\x0c",'%0C').replace("\x0b",'%0B').replace("\x08",'%08').replace("\x03",'%03')+"""

    </prop>
   </props>
  </property>
 </bean>

 <!-- CRAWL METADATA: including identification of crawler/operator -->
 <bean id="metadata" class="org.archive.modules.CrawlMetadata" autowire="byName">
       <property name="operatorContactUrl" value="[see override above]"/>
       <property name="jobName" value="[see override above]"/>
       <property name="description" value="[see override above]"/>
       <property name="robotsPolicyName" value="ignore"/>
  <!-- <property name="operator" value=""/> -->
  <!-- <property name="operatorFrom" value=""/> -->
  <!-- <property name="organization" value=""/> -->
  <!-- <property name="audience" value=""/> -->
       <property name="userAgentTemplate" 
       """ + 'value="'+UserAgentChoice+' +@OPERATOR_CONTACT_URL@'+'"/>'+ """
         
       
 </bean>
 
 <!-- SEEDS: crawl starting points 
      ConfigString allows simple, inline specification of a moderate
      number of seeds; see below comment for example of using an
      arbitrarily-large external file. -->
 <bean id="seeds" class="org.archive.modules.seeds.TextSeedModule">
     <property name="textSource">
      <bean class="org.archive.spring.ConfigString">
       <property name="value">
        <value>
# [see override above]
        </value>
       </property>
      </bean>
     </property>
 </bean>
 
 <!-- SEEDS ALTERNATE APPROACH: specifying external seeds.txt file in
      the job directory, similar to the H1 approach. 
      Use either the above, or this, but not both. -->
 <!-- 
 <bean id="seeds" class="org.archive.modules.seeds.TextSeedModule">
  <property name="textSource">
   <bean class="org.archive.spring.ConfigFile">
    <property name="path" value="seeds.txt" />
   </bean>
  </property>
 </bean>
  -->
 
 <bean id="acceptSurts" class="org.archive.modules.deciderules.surt.SurtPrefixedDecideRule">
  <!-- <property name="decision" value="ACCEPT"/> -->
  <!-- <property name="seedsAsSurtPrefixes" value="true" /> -->
  <!-- <property name="alsoCheckVia" value="false" /> -->
  <!-- <property name="surtsSourceFile" value="" /> -->
  <!-- <property name="surtsDumpFile" value="${launchId}/surts.dump" /> -->
  <!-- <property name="surtsSource">
        <bean class="org.archive.spring.ConfigString">
         <property name="value">
          <value>
           # example.com
           # http://www.example.edu/path1/
           # +http://(org,example,
          </value>
         </property> 
        </bean>
       </property> -->
 </bean>

 <!-- SCOPE: rules for which discovered URIs to crawl; order is very 
      important because last decision returned other than 'NONE' wins. -->
 <bean id="scope" class="org.archive.modules.deciderules.DecideRuleSequence">
  <!-- <property name="logToFile" value="false" /> -->
  <property name="rules">
   <list>
    <!-- Begin by REJECTing all... -->
    <bean class="org.archive.modules.deciderules.RejectDecideRule" />
    <!-- ...then ACCEPT seeds... -->
    <bean class="org.archive.modules.deciderules.SeedAcceptDecideRule">
    </bean>
    <!-- ...but always ACCEPT those marked as prerequisite for another URI... -->
    <bean class="org.archive.modules.deciderules.PrerequisiteAcceptDecideRule">
    </bean>
    <!-- ...but always REJECT those with unsupported URI schemes -->
    <bean class="org.archive.modules.deciderules.SchemeNotInSetDecideRule">
    </bean>
   </list>
  </property>
 </bean>
 

  
 <!-- CANDIDATE CHAIN --> 
 <!-- first, processors are declared as top-level named beans -->
 <bean id="candidateScoper" class="org.archive.crawler.prefetch.CandidateScoper">
 </bean>
 <bean id="preparer" class="org.archive.crawler.prefetch.FrontierPreparer">
  <!-- <property name="preferenceDepthHops" value="-1" /> -->
  <property name="preferenceEmbedHops" value="0" /> 
  <!-- <property name="canonicalizationPolicy"> 
        <ref bean="canonicalizationPolicy" />
       </property> -->
  <!-- <property name="queueAssignmentPolicy"> 
        <ref bean="queueAssignmentPolicy" />
       </property> -->
  <!-- <property name="uriPrecedencePolicy"> 
        <ref bean="uriPrecedencePolicy" />
       </property> -->
  <!-- <property name="costAssignmentPolicy"> 
        <ref bean="costAssignmentPolicy" />
       </property> -->
 </bean>
 <!-- now, processors are assembled into ordered CandidateChain bean -->
 <bean id="candidateProcessors" class="org.archive.modules.CandidateChain">
  <property name="processors">
   <list>
    <!-- apply scoping rules to each individual candidate URI... -->
    <ref bean="candidateScoper"/>
    <!-- ...then prepare those ACCEPTed to be enqueued to frontier. -->
    <ref bean="preparer"/>
   </list>
  </property>
 </bean>
  
 <!-- FETCH CHAIN --> 
 <!-- first, processors are declared as top-level named beans -->
 <bean id="preselector" class="org.archive.crawler.prefetch.Preselector">
  <!-- <property name="recheckScope" value="false" /> -->
  <!-- <property name="blockAll" value="false" /> -->
  <!-- <property name="blockByRegex" value="" /> -->
  <!-- <property name="allowByRegex" value="" /> -->
 </bean>
 <bean id="preconditions" class="org.archive.crawler.prefetch.PreconditionEnforcer">
  <!-- <property name="ipValidityDurationSeconds" value="21600" /> -->
  <!-- <property name="robotsValidityDurationSeconds" value="86400" /> -->
  <!-- <property name="calculateRobotsOnly" value="false" /> -->
 </bean>
 <bean id="fetchDns" class="org.archive.modules.fetcher.FetchDNS">
  <!-- <property name="acceptNonDnsResolves" value="false" /> -->
  <!-- <property name="digestContent" value="true" /> -->
  <!-- <property name="digestAlgorithm" value="sha1" /> -->
 </bean>
 <!-- <bean id="fetchWhois" class="org.archive.modules.fetcher.FetchWhois">
       <property name="specialQueryTemplates">
        <map>
         <entry key="whois.verisign-grs.com" value="domain %s" />
         <entry key="whois.arin.net" value="z + %s" />
         <entry key="whois.denic.de" value="-T dn %s" />
        </map>
       </property> 
      </bean> -->
 <bean id="fetchHttp" class="org.archive.modules.fetcher.FetchHTTP">
  <!-- <property name="useHTTP11" value="false" /> -->
  <!-- <property name="maxLengthBytes" value="0" /> -->
  <!-- <property name="timeoutSeconds" value="1200" /> -->
  <!-- <property name="maxFetchKBSec" value="0" /> -->
  <!-- <property name="defaultEncoding" value="ISO-8859-1" /> -->
  <!-- <property name="shouldFetchBodyRule"> 
        <bean class="org.archive.modules.deciderules.AcceptDecideRule"/>
       </property> -->
  <!-- <property name="soTimeoutMs" value="20000" /> -->
  <!-- <property name="sendIfModifiedSince" value="true" /> -->
  <!-- <property name="sendIfNoneMatch" value="true" /> -->
  <!-- <property name="sendConnectionClose" value="true" /> -->
  <!-- <property name="sendReferer" value="true" /> -->
  <!-- <property name="sendRange" value="false" /> -->
  <!-- <property name="ignoreCookies" value="false" /> -->
  <!-- <property name="sslTrustLevel" value="OPEN" /> -->
  <!-- <property name="acceptHeaders"> 
        <list>
         <value>Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8</value>
        </list>
       </property>
  -->
  <!-- <property name="httpBindAddress" value="" /> -->
  <!-- <property name="httpProxyHost" value="" /> -->
  <!-- <property name="httpProxyPort" value="0" /> -->
  <!-- <property name="httpProxyUser" value="" /> -->
  <!-- <property name="httpProxyPassword" value="" /> -->
  <!-- <property name="digestContent" value="true" /> -->
  <!-- <property name="digestAlgorithm" value="sha1" /> -->
 </bean>
 <bean id="extractorHttp" class="org.archive.modules.extractor.ExtractorHTTP">
 </bean>
 <bean id="extractorHtml" class="org.archive.modules.extractor.ExtractorHTML">
  <!-- <property name="extractJavascript" value="true" /> -->
  <!-- <property name="extractValueAttributes" value="true" /> -->
  <!-- <property name="ignoreFormActionUrls" value="false" /> -->
  <!-- <property name="extractOnlyFormGets" value="true" /> -->
  <!-- <property name="treatFramesAsEmbedLinks" value="true" /> -->
  <!-- <property name="ignoreUnexpectedHtml" value="true" /> -->
  <!-- <property name="maxElementLength" value="1024" /> -->
  <!-- <property name="maxAttributeNameLength" value="1024" /> -->
  <!-- <property name="maxAttributeValueLength" value="16384" /> -->
 </bean>
 <bean id="extractorCss" class="org.archive.modules.extractor.ExtractorCSS">
 </bean> 
 <bean id="extractorJs" class="org.archive.modules.extractor.ExtractorJS">
 </bean>
 <bean id="extractorSwf" class="org.archive.modules.extractor.ExtractorSWF">
 </bean>    
 <!-- now, processors are assembled into ordered FetchChain bean -->
 <bean id="fetchProcessors" class="org.archive.modules.FetchChain">
  <property name="processors">
   <list>
    <!-- re-check scope, if so enabled... -->
    <ref bean="preselector"/>
    <!-- ...then verify or trigger prerequisite URIs fetched, allow crawling... -->
    <ref bean="preconditions"/>
    <!-- ...fetch if DNS URI... -->
    <ref bean="fetchDns"/>
    <!-- <ref bean="fetchWhois"/> -->
    <!-- ...fetch if HTTP URI... -->
    <ref bean="fetchHttp"/>
    <!-- ...extract outlinks from HTTP headers... -->
    <ref bean="extractorHttp"/>
    <!-- ...extract outlinks from HTML content... -->
    <ref bean="extractorHtml"/>
    <!-- ...extract outlinks from CSS content... -->
    <ref bean="extractorCss"/>
    <!-- ...extract outlinks from Javascript content... -->
    <ref bean="extractorJs"/>
    <!-- ...extract outlinks from Flash content... -->
    <ref bean="extractorSwf"/>
   </list>
  </property>
 </bean>
  
 <!-- DISPOSITION CHAIN -->
 <!-- first, processors are declared as top-level named beans  -->
 <bean id="warcWriter" class="org.archive.modules.writer.WARCWriterProcessor">
  <!-- <property name="compress" value="true" /> -->
  <!-- <property name="prefix" value="IAH" /> -->
  <!-- <property name="suffix" value="${HOSTNAME}" /> -->
  <!-- <property name="maxFileSizeBytes" value="1000000000" /> -->
  <!-- <property name="poolMaxActive" value="1" /> -->
  <!-- <property name="MaxWaitForIdleMs" value="500" /> -->
  <!-- <property name="skipIdenticalDigests" value="false" /> -->
  <!-- <property name="maxTotalBytesToWrite" value="0" /> -->
  <!-- <property name="directory" value="${launchId}" /> -->
  <!-- <property name="storePaths">
        <list>
         <value>warcs</value>
        </list>
       </property> -->
  <!-- <property name="writeRequests" value="true" /> -->
  <!-- <property name="writeMetadata" value="true" /> -->
  <!-- <property name="writeRevisitForIdenticalDigests" value="true" /> -->
  <!-- <property name="writeRevisitForNotModified" value="true" /> -->
 </bean>
 <bean id="candidates" class="org.archive.crawler.postprocessor.CandidatesProcessor">
  <!-- <property name="seedsRedirectNewSeeds" value="true" /> -->
  <!-- <property name="processErrorOutlinks" value="false" /> -->
 </bean>
 <bean id="disposition" class="org.archive.crawler.postprocessor.DispositionProcessor">
  <property name="delayFactor" value="0" />
  <property name="minDelayMs" value="0" />
  <property name="respectCrawlDelayUpToSeconds" value="0" />
  <property name="maxDelayMs" value="0" />
  <!-- <property name="maxPerHostBandwidthUsageKbSec" value="0" /> -->
 </bean>
 <!-- <bean id="rescheduler" class="org.archive.crawler.postprocessor.ReschedulingProcessor">
       <property name="rescheduleDelaySeconds" value="-1" />
      </bean> -->
 <!-- now, processors are assembled into ordered DispositionChain bean -->
 <bean id="dispositionProcessors" class="org.archive.modules.DispositionChain">
  <property name="processors">
   <list>
    <!-- write to aggregate archival files... -->
    <ref bean="warcWriter"/>
    <!-- ...send each outlink candidate URI to CandidateChain, 
         and enqueue those ACCEPTed to the frontier... -->
    <ref bean="candidates"/>
    <!-- ...then update stats, shared-structures, frontier decisions -->
    <ref bean="disposition"/>
    <!-- <ref bean="rescheduler" /> -->
   </list>
  </property>
 </bean>
 
 <!-- CRAWLCONTROLLER: Control interface, unifying context -->
 <bean id="crawlController" 
   class="org.archive.crawler.framework.CrawlController">
  <!-- <property name="maxToeThreads" value="25" /> -->
  <!-- <property name="pauseAtStart" value="true" /> -->
  <!-- <property name="runWhileEmpty" value="false" /> -->
  <!-- <property name="recorderInBufferBytes" value="524288" /> -->
  <!-- <property name="recorderOutBufferBytes" value="16384" /> -->
  <!-- <property name="scratchDir" value="scratch" /> -->
 </bean>
 
 <!-- FRONTIER: Record of all URIs discovered and queued-for-collection -->
 <bean id="frontier" 
   class="org.archive.crawler.frontier.BdbFrontier">
  <!-- <property name="queueTotalBudget" value="-1" /> -->
  <!-- <property name="balanceReplenishAmount" value="3000" /> -->
  <!-- <property name="errorPenaltyAmount" value="100" /> -->
  <!-- <property name="precedenceFloor" value="255" /> -->
  <!-- <property name="queuePrecedencePolicy">
        <bean class="org.archive.crawler.frontier.precedence.BaseQueuePrecedencePolicy" />
       </property> -->
  <!-- <property name="snoozeLongMs" value="300000" /> -->
       <property name="retryDelaySeconds" value="30" />
       <property name="maxRetries" value="5" />
  <!-- <property name="recoveryLogEnabled" value="true" /> -->
       <property name="maxOutlinks" value="150000" />
       <property name="extractIndependently" value="true" />
  <!-- <property name="outbound">
        <bean class="java.util.concurrent.ArrayBlockingQueue">
         <constructor-arg value="200"/>
         <constructor-arg value="true"/>
        </bean>
       </property> -->
  <!-- <property name="inbound">
        <bean class="java.util.concurrent.ArrayBlockingQueue">
         <constructor-arg value="40000"/>
         <constructor-arg value="true"/>
        </bean>
       </property> -->
  <!-- <property name="dumpPendingAtClose" value="false" /> -->
 </bean>
 
 <!-- URI UNIQ FILTER: Used by frontier to remember already-included URIs --> 
 <bean id="uriUniqFilter" 
   class="org.archive.crawler.util.BdbUriUniqFilter">
 </bean>
 
 <!--
   EXAMPLE SETTINGS OVERLAY SHEETS
   Sheets allow some settings to vary by context - usually by URI context,
   so that different sites or sections of sites can be treated differently. 
   Here are some example Sheets for common purposes. The SheetOverlaysManager
   (below) automatically collects all Sheet instances declared among the 
   original beans, but others can be added during the crawl via the scripting 
   interface.
  -->
  
<bean id=\'forceRetire\' class=\'org.archive.spring.Sheet\'>
 <property name=\'map\'>
  <map>
   <entry key=\'disposition.forceRetire\' value=\'true\'/>
  </map>
 </property>
</bean>

<bean id='smallBudget' class='org.archive.spring.Sheet'>
 <property name='map'>
  <map>
   <entry key='frontier.balanceReplenishAmount' value='20'/>
   <entry key='frontier.queueTotalBudget' value='100'/>
  </map>
 </property>
</bean>

<!-- veryPolite: any URI to which this sheet's settings are applied 
     will cause its queue to take extra-long politeness snoozes -->
<bean id='veryPolite' class='org.archive.spring.Sheet'>
 <property name='map'>
  <map>
   <entry key='disposition.delayFactor' value='10'/>
   <entry key='disposition.minDelayMs' value='10000'/>
   <entry key='disposition.maxDelayMs' value='1000000'/>
   <entry key='disposition.respectCrawlDelayUpToSeconds' value='3600'/>
  </map>
 </property>
</bean>

<!-- highPrecedence: any URI to which this sheet's settings are applied 
     will give its containing queue a slightly-higher than default 
     queue precedence value. That queue will then be preferred over 
     other queues for active crawling, never waiting behind lower-
     precedence queues. -->
<bean id='highPrecedence' class='org.archive.spring.Sheet'>
 <property name='map'>
  <map>
   <entry key='frontier.balanceReplenishAmount' value='20'/>
   <entry key='frontier.queueTotalBudget' value='100'/>
  </map>
 </property>
</bean>

<!--
   EXAMPLE SETTINGS OVERLAY SHEET-ASSOCIATION
   A SheetAssociation says certain URIs should have certain overlay Sheets
   applied. This example applies two sheets to URIs matching two SURT-prefixes.
   New associations may also be added mid-crawl using the scripting facility.
  -->

<!--
<bean class='org.archive.crawler.spring.SurtPrefixesSheetAssociation'>
 <property name='surtPrefixes'>
  <list>
   <value>http://(org,example,</value>
   <value>http://(com,example,www,)/</value>
  </list>
 </property>
 <property name='targetSheetNames'>
  <list>
   <value>veryPolite</value>
   <value>smallBudget</value>
  </list>
 </property>
</bean>
-->

 <!-- 
   OPTIONAL BUT RECOMMENDED BEANS
  -->
  
 <!-- ACTIONDIRECTORY: disk directory for mid-crawl operations
      Running job will watch directory for new files with URIs, 
      scripts, and other data to be processed during a crawl. -->
 <bean id="actionDirectory" class="org.archive.crawler.framework.ActionDirectory">
  <!-- <property name="actionDir" value="action" /> -->
  <!-- <property name="doneDir" value="${launchId}/actions-done" /> -->
  <!-- <property name="initialDelaySeconds" value="10" /> -->
  <!-- <property name="delaySeconds" value="30" /> -->
 </bean> 
 
 <!--  CRAWLLIMITENFORCER: stops crawl when it reaches configured limits -->
 <bean id="crawlLimiter" class="org.archive.crawler.framework.CrawlLimitEnforcer">
  <!-- <property name="maxBytesDownload" value="0" /> -->
  <!-- <property name="maxDocumentsDownload" value="0" /> -->
  <!-- <property name="maxTimeSeconds" value="0" /> -->
 </bean>
 
 <!-- CHECKPOINTSERVICE: checkpointing assistance -->
 <bean id="checkpointService" 
   class="org.archive.crawler.framework.CheckpointService">
  <!-- <property name="checkpointIntervalMinutes" value="-1"/> -->
  <!-- <property name="checkpointsDir" value="checkpoints"/> -->
 </bean>
 
 <!-- 
   OPTIONAL BEANS
    Uncomment and expand as needed, or if non-default alternate 
    implementations are preferred.
  -->
  
 <!-- CANONICALIZATION POLICY -->
 <!--
 <bean id="canonicalizationPolicy" 
   class="org.archive.modules.canonicalize.RulesCanonicalizationPolicy">
   <property name="rules">
    <list>
     <bean class="org.archive.modules.canonicalize.LowercaseRule" />
     <bean class="org.archive.modules.canonicalize.StripUserinfoRule" />
     <bean class="org.archive.modules.canonicalize.StripWWWNRule" />
     <bean class="org.archive.modules.canonicalize.StripSessionIDs" />
     <bean class="org.archive.modules.canonicalize.StripSessionCFIDs" />
     <bean class="org.archive.modules.canonicalize.FixupQueryString" />
    </list>
  </property>
 </bean>
 -->
 

 <!-- QUEUE ASSIGNMENT POLICY -->
 <!--
 <bean id="queueAssignmentPolicy" 
   class="org.archive.crawler.frontier.SurtAuthorityQueueAssignmentPolicy">
  <property name="forceQueueAssignment" value="" />
  <property name="deferToPrevious" value="true" />
  <property name="parallelQueues" value="1" />
 </bean>
 -->
 
 <!-- URI PRECEDENCE POLICY -->
 <!--
 <bean id="uriPrecedencePolicy" 
   class="org.archive.crawler.frontier.precedence.CostUriPrecedencePolicy">
 </bean>
 -->
 
 <!-- COST ASSIGNMENT POLICY -->
 <!--
 <bean id="costAssignmentPolicy" 
   class="org.archive.crawler.frontier.UnitCostAssignmentPolicy">
 </bean>
 -->
 
 <!-- CREDENTIAL STORE: HTTP authentication or FORM POST credentials -->
 <!-- 
 <bean id="credentialStore" 
   class="org.archive.modules.credential.CredentialStore">
 </bean>
 -->
 
 <!-- DISK SPACE MONITOR: 
      Pauses the crawl if disk space at monitored paths falls below minimum threshold -->
 <!-- 
 <bean id="diskSpaceMonitor" class="org.archive.crawler.monitor.DiskSpaceMonitor">
   <property name="pauseThresholdMiB" value="500" />
   <property name="monitorConfigPaths" value="true" />
   <property name="monitorPaths">
     <list>
       <value>PATH</value>
     </list>
   </property>
 </bean>
 -->
 
 <!-- 
   REQUIRED STANDARD BEANS
    It will be very rare to replace or reconfigure the following beans.
  -->

 <!-- STATISTICSTRACKER: standard stats/reporting collector -->
 <bean id="statisticsTracker" 
   class="org.archive.crawler.reporting.StatisticsTracker" autowire="byName">
  <!-- <property name="reports">
        <list>
         <bean id="crawlSummaryReport" class="org.archive.crawler.reporting.CrawlSummaryReport" />
         <bean id="seedsReport" class="org.archive.crawler.reporting.SeedsReport" />
         <bean id="hostsReport" class="org.archive.crawler.reporting.HostsReport" />
         <bean id="sourceTagsReport" class="org.archive.crawler.reporting.SourceTagsReport" />
         <bean id="mimetypesReport" class="org.archive.crawler.reporting.MimetypesReport" />
         <bean id="responseCodeReport" class="org.archive.crawler.reporting.ResponseCodeReport" />
         <bean id="processorsReport" class="org.archive.crawler.reporting.ProcessorsReport" />
         <bean id="frontierSummaryReport" class="org.archive.crawler.reporting.FrontierSummaryReport" />
         <bean id="frontierNonemptyReport" class="org.archive.crawler.reporting.FrontierNonemptyReport" />
         <bean id="toeThreadsReport" class="org.archive.crawler.reporting.ToeThreadsReport" />
        </list>
       </property> -->
  <!-- <property name="reportsDir" value="${launchId}/reports" /> -->
  <!-- <property name="liveHostReportSize" value="20" /> -->
  <!-- <property name="intervalSeconds" value="20" /> -->
  <!-- <property name="keepSnapshotsCount" value="5" /> -->
  <!-- <property name="liveHostReportSize" value="20" /> -->
 </bean>
 
 <!-- CRAWLERLOGGERMODULE: shared logging facility -->
 <bean id="loggerModule" 
   class="org.archive.crawler.reporting.CrawlerLoggerModule">
  <!-- <property name="path" value="${launchId}/logs" /> -->
  <!-- <property name="crawlLogPath" value="crawl.log" /> -->
  <!-- <property name="alertsLogPath" value="alerts.log" /> -->
  <!-- <property name="progressLogPath" value="progress-statistics.log" /> -->
  <!-- <property name="uriErrorsLogPath" value="uri-errors.log" /> -->
  <!-- <property name="runtimeErrorsLogPath" value="runtime-errors.log" /> -->
  <!-- <property name="nonfatalErrorsLogPath" value="nonfatal-errors.log" /> -->
  <!-- <property name="logExtraInfo" value="false" /> -->
 </bean>
 
 <!-- SHEETOVERLAYMANAGER: manager of sheets of contextual overlays
      Autowired to include any SheetForSurtPrefix or 
      SheetForDecideRuled beans -->
 <bean id="sheetOverlaysManager" autowire="byType"
   class="org.archive.crawler.spring.SheetOverlaysManager">
 </bean>

 <!-- BDBMODULE: shared BDB-JE disk persistence manager -->
 <bean id="bdb" 
  class="org.archive.bdb.BdbModule">
  <!-- <property name="dir" value="state" /> -->
  <!-- if neither cachePercent or cacheSize are specified (the default), bdb
       uses its own default of 60% -->
  <!-- <property name="cachePercent" value="0" /> -->
  <!-- <property name="cacheSize" value="0" /> -->
  <!-- <property name="useSharedCache" value="true" /> -->
  <!-- <property name="expectedConcurrency" value="25" /> -->
 </bean>
 
 <!-- BDBCOOKIESTORAGE: disk-based cookie storage for FetchHTTP -->
 <bean id="cookieStorage" 
   class="org.archive.modules.fetcher.BdbCookieStorage">
  <!-- <property name="cookiesLoadFile"><null/></property> -->
  <!-- <property name="cookiesSaveFile"><null/></property> -->
  <!-- <property name="bdb">
        <ref bean="bdb"/>
       </property> -->
 </bean>
 
 <!-- SERVERCACHE: shared cache of server/host info -->
 <bean id="serverCache" 
   class="org.archive.modules.net.BdbServerCache">
  <!-- <property name="bdb">
        <ref bean="bdb"/>
       </property> -->
 </bean>

 <!-- CONFIG PATH CONFIGURER: required helper making crawl paths relative
      to crawler-beans.cxml file, and tracking crawl files for web UI -->
 <bean id="configPathConfigurer" 
   class="org.archive.spring.ConfigPathConfigurer">
 </bean>
 
</beans>
"""
    #heritrix it
    log_add('Beginning heritrix processing')
    global sslTypeS
    hlog_add(run('curl'+sslTypeS+' -v -d "action=Exit+Java+Process&im_sure=on" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine')[0])
    os.system('mkdir h3/heritrix-3.1.1/jobs/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG+'/')
    hres=''
    #hlog_add(run('curl -v -d "createpath='+'AMJ_BarrelData_'+barrelID+'_' + uuidG+'&action=create" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine')[0])
    f = open('h3/heritrix-3.1.1/jobs/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG+'/crawler-beans.cxml', 'w')
    f.write(job_data)
    f.close()
    #The port: 42643=ARCAE=ARCMAJ3 missing some letters
    hlog_add(run('h3/heritrix-3.1.1/bin/heritrix -a admin -p '+HerWebPort)[0])
    hlog_add(run('curl'+sslTypeS+' -v -d "action=build" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine/job/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG)[0])
    hlog_add(run('curl'+sslTypeS+' -v -d "action=launch" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine/job/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG)[0])
    time.sleep(0.5)
    hlog_add(run('curl'+sslTypeS+' -v -d "action=unpause" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine/job/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG)[0])
    # poll job dir for completion
    jobFinished=False
    while not jobFinished:
        jobState=open('h3/heritrix-3.1.1/jobs/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG + '/job.log', 'r').read()
        if 'INFO FINISHED' in jobState:
            hlog_add('Heritrix finished')
            print 'Heritrix finished'
            jobFinished=True
        else:
            time.sleep(15)
            rubles=run('tail -n 2 h3/heritrix-3.1.1/jobs/AMJ_BarrelData_'+barrelID+'_' + uuidG + '/job.log')[0]
            hlog_add(rubles)
            print rubles
            hlog_add('Heritrix not finished')
            print 'Heritrix not finished'
            run('h3/heritrix-3.1.1/bin/heritrix -a admin -p '+HerWebPort)
            run('curl'+sslTypeS+' -v -d "action=build" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine/job/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG)
            run('curl'+sslTypeS+' -v -d "action=launch" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine/job/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG)
            time.sleep(0.5)
            run('curl'+sslTypeS+' -v -d "action=unpause" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine/job/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG)
    hlog_add(run('curl'+sslTypeS+' -v -d "action=teardown" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine/job/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG)[0])
    hlog_add(run('curl'+sslTypeS+' -v -d "action=Exit+Java+Process&im_sure=on" -k -u admin:admin --anyauth --location https://localhost:'+HerWebPort+'/engine')[0])
    os.system('mv h3/heritrix-3.1.1/jobs/'+'AMJ_BarrelData_'+barrelID+'_' + uuidG+' .')
    os.system('mv '+'AMJ_BarrelData_'+barrelID+'_' + uuidG+'/latest/warcs/*.warc.gz .')
    #hlog_add(hres)
    os.system('mv amc_H3_log-'+timeRunning+'.log ./AMJ_BarrelData_'+barrelID+'_' + uuidG+'/')

    #'amc_H3_log-'+timeRunning+'.log'
    errored = False
    run('7z a '+'AMJ_BarrelData_'+barrelID+'_' + uuidG+'_H3.7z '+'AMJ_BarrelData_'+barrelID+'_' + uuidG+'/')
    if not errored:
        os.system('rm -rf AMJ_BarrelData_'+barrelID+'_' + uuidG+'/')
    else:
        log_add('Encountered an error zipping Heritrix data')
    log_add('Finished Heritrix processing')
    # done Heritrix work
    pageLinks=run('bash -c \'grep --binary-files=text "Saving to:" log-'+timeRunning+'.log;\';')[1].replace('Saving to: ‘','')
    pageLinks2='http://'+pageLinks.replace('’','').replace('\n','\nhttp://')
    pageLinks3=run('bash -c \'grep --binary-files=text "^--" log-'+timeRunning+'.log;\';')[1]
    pageLSplit1=pageLinks3.split("\n")
    pageLSplit2=[]
    for entry in pageLSplit1:
        pageLSplit2.append(entry[25:]+"\n")
    pageLinks4=''.join(pageLSplit2)
    #pageLinks4=re.sub("^.{25}",'',pageLinks3)
    pageLinks5=pageLinks2+pageLinks4+'\n'
    log_add('\n\nEXTRACTED LINKS FROM DATA. Link results: \n\n' + pageLinks5 + '\n\n')
    #linkGz = StringIO.StringIO()
    #gzip_file = gzip.GzipFile(fileobj=linkGz, mode='w')
    #gzip_file.write(pageLinks5)
    #gzip_file.close()
    #f = open('URLs.lst', 'w')
    #f.write(linkGz.getvalue())
    #f.close()
    logDataRes=open('log-'+timeRunning+'.log', 'r').read()
    logDataList=logDataRes.split("\n--")
    failedUrlsList=[]
    for record in logDataList:
        llRecord=record.split("\n")
        record1l=llRecord[0]
        print 'Record: ' + record
        if '200 OK' not in record:
            failedUrlsList.append(record1l[23:]+"\n")
    elogDataRes=open('log-'+timeRunning+'.log', 'r').read()
    elogDataList=elogDataRes.split('Getting URL')
    print elogDataList
    for erecord in elogDataList:
        ellRecord=erecord.split("\n")
        #print ellRecord
        #print 'Doom'
        erecord1l=ellRecord[0]
        #print erecord1l
        eluRecord=erecord1l.split(" ")
        try:
            erecordu=eluRecord[4]
        except:
            #This record did not fetch a URL
            pass
        #print 'Record: ' + erecord
        if '200 OK' not in erecord:
            failedUrlsList.append(erecordu+"\n")
    failedUrlsListu=list(set(failedUrlsList))
    failedUrls=''.join(failedUrlsListu)
    failedUrl_count=len(failedUrlsListu)
    failedGz = StringIO.StringIO()
    gzip_file = gzip.GzipFile(fileobj=failedGz, mode='w')
    gzip_file.write(failedUrls)
    gzip_file.close()
    f = open('failed.lst', 'w')
    f.write(failedGz.getvalue())
    f.close()
    log_add('\n\nEXTRACTED FAILED URLS FROM DATA. Failed URL list: \n\n' + failedUrls + '\n\n')
    log_add('Removing empty directories...')
    run('find . -empty -type d -delete')
    #f = open('linkResults.txt', 'a')
    #f.write(pageLinks5+"\n")
    #f.close()
    #active.php?handler=1&handlerNeeded=arcmaj3&amtask=up
    #curl = ['curl', '--location']
    #curl += [ '--data', '\'handler=1&handlerNeeded=arcmaj3&amtask=up&uploadedBarrelData='+pageLinks5.encode('base64')+'&failedUrlData='+failedUrls.encode('base64')+'\'', '-A', "'"+UserAgentChoice+"'" ]
    #curl += [ "http://localhost:8888/d/r/active.php" ]
    #curlline = ' '.join(curl)
    #log_add('Executing curl request: ')
    #log_add(curlline+'\n')
    #Merge available files.
    concatW()
    #Extract data from megawarc
    run("bash -c 'gunzip -c AMJ_BarrelData_"+barrelID+"_" + uuidG+".megawarc.warc.gz >tmp_"+uuidG+".dat'")
    #her_ext_url_rdata=open('tmp_'+uuidG+'.dat', 'r').read()
    her_ext_url_rdata=run("bash -c 'cat tmp_"+uuidG+".dat | grep --binary-files=text outlink'")[0]
    ulog_add("Extracted Heritrix URL data: \n\n"+her_ext_url_rdata+"\n\n")
    run("bash -c 'rm tmp_*.dat'")
    her_listData=her_ext_url_rdata.split("\n")
    #print her_listData
    her_urlListDataL=[]
    for hLdRe in her_listData:
        hlRecord=hLdRe.split(" ")
        #print hlRecord
        try:
            hlUrl=hlRecord[1]
        except:
            ulog_add('Invalid record ' + str(hlRecord) + '. Probably not a big deal.')
        her_urlListDataL.append(hlUrl)
    her_ext_url_data="\n".join(her_urlListDataL)
    #f = open('URLs.lst', 'r')
    #f.write("\n"+her_ext_url_data+"\n")
    #f.close()
    pageLinksDHerFinala=pageLinks5+"\n"+her_ext_url_data
    plFinList=pageLinksDHerFinala.split('\n')
    plFinListD=list(set(plFinList))
    pageLinksDHerFinal=barrelID+'\n'+userName+'\n'+"\n".join(plFinListD)
    outlink_count = len(plFinListD)
    linkGz = StringIO.StringIO()
    gzip_file = gzip.GzipFile(fileobj=linkGz, mode='w')
    gzip_file.write(pageLinksDHerFinal)
    gzip_file.close()
    f = open('URLs.lst', 'w')
    f.write(linkGz.getvalue())
    f.close()
    ulog_add("Finished extracted URL data: \n\n" + pageLinksDHerFinal)
    #Upload megawarc.
    #Upload barrel data back to base.
    ulog_add('\n\nUploading barrel data back to base.\n\n');
    upload(wikis)
    if statDuro:
        ulog_add('Removing remaining files\n')
        os.system('rm *.warc.gz *.7z *.xz upload-* bucketsCompleted-* barrelsCompleted-* log-* URLs.lst failed.lst *.megawarc.tar *.megawarc.json.gz')
    ulog_add('Sleeping 30 seconds to give IA a chance to catch up…')
    time.sleep(30)
    postdata='handler=1&handlerNeeded=arcmaj3&amtask=up'+'&barrelSize='+str(int(barrelSize))+''+'&verd='+verd+'&amloc='+'AMJ_BarrelData_'+barrelID+'_' + uuidG +'.' +timeRunning
    ulog_add("\n\nPOST data sent to http://localhost:8888/d/r/active.php: \n\n"+postdata+"\n\n(Barrel ID: "+barrelID+")\n\n")
    ulog_add("\n\nWaiting for reply from server for barrel " + str(barrelID) + ": " + str(outlink_count) + " outlinks; " + str(failedUrl_count) + " failed URLs\n\n")
    req = urllib2.Request('http://localhost:8888/d/r/active.php', postdata)
    req.add_header('User-agent',UserAgentChoice)
    fp = urllib2.urlopen(req)
    #errored = False
    #uploadBarrelResult = run(curlline)[0]
    #log_add(uploadBarrelResult)
    ulog_add(fp.read())
    #Upload logs?
#if  __name__ == "__main__":
main()

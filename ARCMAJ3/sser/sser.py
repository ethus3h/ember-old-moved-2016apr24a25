#!/usr/bin/python
# -*- coding: utf-8 -*-
# sser
# Version 0.1, 2014.nov.29.
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

# 1. ../Media.sserdb/snapshots/{{../Media.sserdb/latest}++}/time <- {time} <- now
# 2. {records} <- list of everything and its shasum --algorithm 512 hash.
# 3. foreach {records} as {item}:
#   I. If ../Media.sserdb/hashesdb/{hash}.exists:
#     A. break; # file already in repo
#   II. Else: #new file since last snapshot
#     A. ../Media.sserdb/encdb/enctmp <- {item}.aes256()
# 	  B. ../Media.sserdb/hashesdb/{hash} <- {enctmp.sha512()}
#     C. Hardlink ../Media.sserdb/encdb/{enctmp.sha512()} to ../Media.sserdb/encdb/enctmp
#     D. Upload ../Media.sserdb/encdb/{enctmp.sha512()} to ia:Collistar_sser_pack_{../Media.sserdb/UUID}_{{../Media.sserdb/latest}++}
#     E. Delete ../Media.sserdb/encdb/enctmp
#     F. ../Media.sserdb/ehdb/{enctmp.sha512()} <- {hash}
# 4. Clone directory tree to ../Media.sserdb/snapshots/{{../Media.sserdb/latest}++}/d/
# 5. foreach {records} as {item}:
#   I. {item.name} <- "http://archive.org/download/Collistar_sser_pack_{../Media.sserdb/UUID}_{{../Media.sserdb/latest}++}/{enctmp.sha512()}"
# 6. ../.tmp.{../Media.sserdb/UUID}.{time} <- ../Media.sserdb/snapshots/{{../Media.sserdb/latest}++}/.pax().bzip2().aes256()
# 7. Upload ../.tmp.{../Media.sserdb/UUID}.{time} to ia:Collistar_sser_pack_{../Media.sserdb/UUID}_{{../Media.sserdb/latest}++}
# 8. Delete ../.tmp.{../Media.sserdb/UUID}.{time}
# Keys: http://archive.org/account/s3.php
# Documentation: http://archive.org/help/abouts3.txt
# https://wiki.archive.org/twiki/bin/view/Main/IAS3BulkUploader
# http://en.ecgpedia.org/api.php?action=query&meta=siteinfo&siprop=general|rightsinfo&format=xml
# TODO: bug - upload may (partly) fail if two (small) files are sent to s3 without pause http://p.defau.lt/?puN_G_zKXbv1lz9TfSliPg http://archive.org/details/wiki-editionorg_w or something http://p.defau.lt/?udwrG7YQn4RK_1whl1XWRw http://archive.org/details/wiki-jutedreamhosterscom_lineageii_bestiary
# TODO: minor bug - don't overwrite existing files with same filename in the same identifier

# You need a file named config.txt with username, access key, secret key, and a title for the uploaded items, each in its own line.
userName=open('config.txt', 'r').readlines()[0].strip()
accesskey = open('config.txt', 'r').readlines()[1].strip()
secretkey = open('config.txt', 'r').readlines()[2].strip()
title = open('config.txt', 'r').readlines()[3].strip()
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
def shellesc(s):
    return s.replace("'", "%27").replace(' ','%20').replace('<','%3C').replace('>','%3E').replace('[','%5B').replace(']','%5D').replace('(','%28').replace(')','%29').replace(';','%3B').replace("\x00",'%00').replace("\x0c",'%0C').replace("\x0b",'%0B').replace("\x08",'%08').replace("\x03",'%03')

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

uuidG=str(uuid.uuid4())
convertlang = {'ar': 'Arabic', 'de': 'German', 'en': 'English', 'es': 'Spanish', 'fr': 'French', 'it': 'Italian', 'ja': 'Japanese', 'nl': 'Dutch', 'pl': 'Polish', 'pt': 'Portuguese', 'ru': 'Russian'}
timeRunning=now.strftime("%Y-%m-%d-%H-%M-%S-%f-%Z_E")
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
def log(wiki, dump, msg):
    global timeRunning
    f = open('uploader-'+timeRunning+'.log', 'a')
    f.write('\n%s;%s;%s' % (wiki, dump, msg))
    f.close()
timeFetchResult=run('bash -c \'wget --no-check-certificate --warc-file='+timeRunning+'.Now -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m-\\d-\\H-\\M-\\S-\\6N-\\z"\'')[0]

with open ("now.txt", "r") as timeFile:
    timeRemote=timeFile.read()
log_add("\Current time fetch output: \n"+timeFetchResult+"\n\n")
log_add("\nCurrent time retrieved remotely: \n"+timeRemote+"\n\n")
statDuro=False
def upload(wikis):
    global uuidG
    global errored
    global timeRunning
    global title
    global statDuro
    log_add(wikis)
    log_add("#"*73)
    log_add("# Uploading record")
    log_add("#"*73)
    dumps = []
    for dirname, dirnames, filenames in os.walk('.'):
        if dirname == '.':
            for f in filenames:
                #log_add('Filenames: ' + str(f))
                if (f.endswith('.json') or f.endswith('.warc.gz') or ('megawarc' in f and (f.endswith('.tar') or f.endswith('.json.gz') or f.endswith('.warc.gz')))):
                    dumps.append(f)
                    #dumps.append(f)
            break
    log_add(dumps)
    c = 0
    for dump in dumps:
        dumpid='WARCdealer_BarrelData_'+title+'_' + uuidG +'.' +timeRunning
        log_add("#"*73)
        log_add('ATTEMPTING TO UPLOAD DUMP DATA: ' + dump)
        log_add('DUMP ID: ' + dumpid)
        log_add("#"*73)
        time.sleep(0.1)
        wikititle = "WARCdealer pack. WARC in set labeled: "+ title + ', ID: ' + uuidG
        wikidesc = "WARCdealer pack. WARC in set labeled: "+ title + ', ID ' + uuidG+'. '+title+'_' + uuidG +'.' +timeRunning
        wikikeys = ['Arcmaj3','WARC','snapshot','archive','WARCdealer','WARCdealer pack', title, title+'_' + uuidG +'.' +timeRunning]                        
    	barrelSize = int(os.path.getsize(dump))
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
        log_add('Executing curl request: ')
        log_add(curlline+'\n')
        errored = False
        uploadFetchResultB = run(curlline)[0]
        log_add('\n\ncurl request result:\n'+uploadFetchResultB+'\n\n')
        c += 1
        if not (errored or 'XML' in uploadFetchResultB or 'xml' in uploadFetchResultB or 'html' in uploadFetchResultB or 'HTML' in uploadFetchResultB):
            os.system('rm '+dump)
            log_add('Removing file: '+dump+'\n')
            statDuro = True
        else:
            log_add('ERROR UPLOADING BARREL. THIS IS NOT GOOD.')
        errored = False
        log_add('Logging added item: ' + 'https://archive.org/details/' + dumpid + '\n\n\n\n\n')

def concatW():
    global errored
#     log_add("\n--\n"+"#"*73)
#     log_add("# CONCATENATING RECORDS")
#     log_add("#"*73)
    errored = False
    #based on "# find / -iname "*.mp3" -type f -exec /bin/mv {} /mnt/mp3 \;" from http://www.cyberciti.biz/tips/howto-linux-unix-find-move-all-mp3-file.html
    #find . -iname "*.warc.gz" -type f -exec /bin/mv {} . \;
    run('bash -c \'find . -iname "*.warc.gz" -type f -exec /bin/mv {} . \;\';')
#     cctRes=run('bash -c \'./megawarc pack WARCdealer_'+title+'_' + uuidG +'.' +timeRunning+'_' + uuidG+' *.warc.gz *.megawarc.tar *.megawarc.json.gz;\';')
#     log_add("\n\n"+cctRes[0]+"\n\n")
#     log_add("\n--\n"+"#"*73)
#     log_add("# DONE CONCATENATING RECORDS")
#     log_add("#"*73)
#     if not errored:
#         log_add("Removing records…")
#         dumps = []
#         for dirname, dirnames, filenames in os.walk('.'):
#             if dirname == '.':
#                 for f in filenames:
#                     if (f.endswith('.warc.gz') and not 'megawarc' in f):
#                         dumps.append(f)
#                 break
#         for dump in dumps:
#             log_add("\n\n")
#             log_add('Removing file: '+dump+'\n')
#             os.system('rm '+dump)
#             log_add("\n\nAppend finished\n\n")
#     else:
#         log_add('ERROR CONCATENATING RECORDS. THIS IS NOT GOOD.')
#     log_add('\n\nCompressing records…\n\n')
#     log_add(run('xz WARCdealer_'+title+'_' + uuidG +'.' +timeRunning+'_' + uuidG+'.megawarc.tar')[0])
#     log_add('\n\nFinished compressing records…\n\n')
#     errored = False
#     return cctRes[0]
log_add('\nPreparing main function\n')

def main():
    log_add('\nEntering main function\n')
    wikis = ''
    global errored
    global uuidG
    global verd
    global UserAgentChoice
    global userName
    global timeRunning
    iId=1
    concatW()
    log_add('\n\nUploading barrel data back to base.\n\n');
    upload(wikis)
    log_add('Sleeping 12000 seconds')
    time.sleep(12000)
main()
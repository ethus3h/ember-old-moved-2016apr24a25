#!/usr/bin/python
# -*- coding: utf-8 -*-
# WARCdealer
# Version 1.2.7, 6 January 2016.
#
# Copyright (C) 2011-2012 WikiTeam
# Arcmaj3 additions copyright 2013–2016 Futuramerlin
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
# TODO: bug - upload may (partly) fail if two (small) files are sent to
# TODO: minor bug - don't overwrite existing files with same filename
#    s3 without pause in the same identifier

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
import traceback
import random

# CONFIGURATION
# You need a file named config.txt with username, access key, secret key, and a
# title for the uploaded items, each in its own line.
userName = open('config.txt', 'r').readlines()[0].strip()
accesskey = open('config.txt', 'r').readlines()[1].strip()
secretkey = open('config.txt', 'r').readlines()[2].strip()
title = open('config.txt', 'r').readlines()[3].strip()
collection = 'amjbarreldata'
# Replace "amjbarreldata" with "opensource" if you are not an admin of the
#    collection
# end configuration


def shellesc(s):
    return s.replace(
        "'",
        "%27").replace(
        ' ',
        '%20').replace(
            '<',
            '%3C').replace(
                '>',
                '%3E').replace(
                    '[',
                    '%5B').replace(
                        ']',
                        '%5D').replace(
                            '(',
                            '%28').replace(
                                ')',
                                '%29').replace(
                                    ';',
                                    '%3B').replace(
                                        "\x00",
                                        '%00').replace(
                                            "\x0c",
                                            '%0C').replace(
                                                "\x0b",
                                                '%0B').replace(
                                                    "\x08",
                                                    '%08').replace(
                                                        "\x03",
        '%03')


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

uuidG = str(uuid.uuid4())
timeRunning = now.strftime("%Y-%m-%d-%H-%M-%S-%f-%Z_E")
errored = False


def run(command):
    print command
    global errored
    commandResult = ''
    try:
        commandRes = check_output(command, shell=True,
                                  stderr=subprocess.STDOUT)
        commandResult = "Running command: \n\n" + \
            command + "\n\n\n\n" + commandRes + "\n\n\n\n"
    except Exception as e:
        print 'Error: ' + traceback.format_exc()
        commandRes = ''
        try:
            commandResult = "Running command: \n\n" + command + "\n\n\n\n" + \
                commandResult + str(e.output) + \
                "\n\n\n\nError encountered while running command. " + \
                "This is probably not a big deal.\n\n"
        except Exception as e:
            print 'Error again: ' + traceback.format_exc()
            commandResult = \
                "\n\n\n\nError encountered while running command: \n\n" + \
                command + "\n\n\n\nThis is probably not a big deal. " + \
                "Possibly the command line was incorrectly structured?\n\n"
        errored = True
    return [commandResult, commandRes]


def log_add(text):
    text = str(text)
    print text
    global timeRunning
    f = open('log-' + timeRunning + '.log', 'a')
    f.write(text + "\n")
    f.close()


def log(wiki, dump, msg):
    global timeRunning
    f = open('uploader-' + timeRunning + '.log', 'a')
    f.write('\n%s;%s;%s' % (wiki, dump, msg))
    f.close()
timeFetchResult = run(
    'bash -c \'wget --no-check-certificate --warc-file=' +
    timeRunning +
    '.Now -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m' +
    '-\\d-\\H-\\M-\\S-\\6N-\\z"\'')[0]

with open("now.txt", "r") as timeFile:
    timeRemote = timeFile.read()
log_add("\Current time fetch output: \n" + timeFetchResult + "\n\n")
log_add("\nCurrent time retrieved remotely: \n" + timeRemote + "\n\n")
statDuro = False


def upload(wikis):
    global uuidG
    global errored
    global timeRunning
    global title
    global statDuro
    log_add(wikis)
    log_add("#" * 73)
    log_add("# Uploading record")
    log_add("#" * 73)
    dumps = []
    for dirname, dirnames, filenames in os.walk('.'):
        if dirname == '.':
            for f in filenames:
                if (f.endswith('.json') or f.endswith('.warc') or
                    f.endswith('.warc.gz') or ('megawarc' in f and
                                               (f.endswith('.tar') or
                                                f.endswith('.json.gz') or
                                                f.endswith('.warc.gz')))):
                    dumps.append(f)
            break
    log_add(dumps)
    c = 0
    for dump in dumps:
        log_add("#" * 73)
        log_add('ATTEMPTING TO UPLOAD DUMP DATA: ' + dump)
        log_add('DUMP ID: ' + dumpid)
        log_add("#" * 73)
        time.sleep(0.1)
        global sslTypeS
        run('iu() { IUIDENTIFIER=$(python -c \'import uuid; print str(' +
            'uuid.uuid4())\')-$(date +%Y.%m.%d.%H.%M.%S.%N)-$(xxd -pu' +
            ' <<< "$(date +%z)"); ia upload $IUIDENTIFIER --metadata="' +
            'collection:amjbarreldata; subject:Uploaded using iu v3 for' +
            ' warcdealer; warcdealer; 249707AC-B4EF-11E5-B573-45D037852114;' +
            ' $IUIDENTIFIER" "$@"; echo \'https://archive.org/download/\'' +
            '$IUIDENTIFIER; }')
        log_add('Uploading: ')
        errored = False
        uploadFetchResultB = run(curlline)[1]
        c += 1
        log_add('Errored: ' + str(errored))
        if not (errored):
            os.system('rm ' + dump)
            log_add('Removing file: ' + dump + '\n')
            statDuro = True
        else:
            log_add('ERROR UPLOADING BARREL. THIS IS NOT GOOD.')
        errored = False


def concatW():
    global errored
    errored = False
    run('bash -c \'while IFS= read -r -d \\\'\\\' file\; do mv -v "$file" ' +
        '~/warcdealer/"$(python -c \\\'import uuid\; print str(uuid.uuid4())' +
        '\\\')-$(date \\\'+%Y.%m.%d-%H.%M.%S.%z\\\')"\\\'.warc\\\'\; done < ' +
        '<(find . -type f -name \\*.warc -print0)\;\';')
    run('bash -c \'while IFS= read -r -d \\\'\\\' file\; do mv -v "$file" ' +
        '~/warcdealer/"$(python -c \\\'import uuid\; print str(uuid.uuid4())' +
        '\\\')-$(date \\\'+%Y.%m.%d-%H.%M.%S.%z\\\')"\\\'.warc.gz\\\'\; done' +
        ' < <(find . -type f -name \\*.warc.gz -print0)\;\';')

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
    iId = 1
    concatW()
    log_add('\n\nUploading barrel data back to base.\n\n')
    upload(wikis)
    log_add('Sleeping 180 seconds (3 minutes)')
    time.sleep(180)

main()

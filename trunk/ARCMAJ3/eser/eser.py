#!/usr/bin/python
# -*- coding: utf-8 -*-
# eser — the Ember SnapshottER
# Version 0.1, 2015-jan-08 and 2015-jan-08a09.
#
# Copyright 2014 Elliot Wallace
# Portions copyright (C) 2011-2012 WikiTeam
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


# ABOUT THIS PROGRAM: DOCUMENTATION
# This is a tool for storing revisions of a folder in the cloud. To use it, make a folder to version
# called "Archive". Then, run:
# python ./Archive/Meta/eser.py
# in the enclosing directory. Make sure you have lots (more than the
# size of the Archive folder) of disk space available. Follow the instructions given.
# To get your API keys for uploading, visit:
# http://archive.org/account/s3.php

# The API interface code used in this program was written by WikiTeam.
# Additionally, standard Web site resources were consulted in its creation. Code that is
# directly used or based on a Web resource is marked with a comment by its URL.

import os
import uuid
import time
import urllib
import traceback
import subprocess
import sys

# Ask user if they want to make a new snapshot, or make a new patch.
# If snapshot, copy Archive to Archive.stable.
# If snapshot, upload Archive to IA.
# If patch, write patch file, upload it to IA, and delete it.
# Write IA mirror URL to Meta folder.

print 'Welcome to eser. Please run eser as root.'

os.system('mkdir ./Archive/Meta/')
os.system('mkdir ./Archive/Meta/Revisions/')
os.system('mkdir ./Archive/Meta/Revisions/Logs/')
os.system('mkdir ./Archive/Meta/Revisions/Archive.eserdb')
os.system('mkdir ./Archive/Meta/Revisions/Archive.eserdb/meta/')
os.system('mkdir ./Archive/Meta/Revisions/Archive.eserdb/snapshots/')

#Command definitions
errored = False
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
def log_add(text):
    text = str(text)
    print text
    global ltime
    f = open('./Archive/Meta/Revisions/Logs/log-'+ltime+'.log', 'a')
    f.write(text+"\n")
    f.close()
# http://amix.dk/blog/post/19408
def timeout_command(command, timeout):
    """call shell-command and either return its output or kill it
    if it doesn't normally exit within timeout seconds and return None"""
    import subprocess, datetime, os, time, signal

    cmd = command.split(" ")
    start = datetime.datetime.now()
    process = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

    while process.poll() is None:
        time.sleep(0.1)
        now = datetime.datetime.now()
        if (now - start).seconds > timeout:
            os.kill(process.pid, signal.SIGKILL)
            os.waitpid(-1, os.WNOHANG)
            return None

    return process.stdout.read()
def run(command):
    log_add("Running: "+command)
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
    log_add('Command result: '+commandResult+'\n')
    log_add('Command output: '+commandRes+'\n')
    return [commandResult,commandRes]
#Done command definitions

ltime = time.strftime("%Y.%m.%d.%H.%M.%S.%f.%z", time.gmtime())		
pwd = run('pwd')[1]
run('mkdir ./Archive/Meta/Revisions/Archive.eserdb/snapshots/'+ltime+'/')
run('echo "'+ltime+'" > ./Archive/Meta/Revisions/Archive.eserdb/snapshots/'+ltime+'/localTime')
timefile = urllib.URLopener()
timefn = './Archive/Meta/Revisions/Archive.eserdb/snapshots/'+ltime+'/remoteTime'
try:
	timefile.retrieve("http://www.timeapi.org/utc/now?format=%25Y.%25m.%25d.%25H.%25M.%25S.%25Z", timefn)
except:
	try:
		timefile.retrieve("http://www.timeapi.org/utc/now?format=%25Y.%25m.%25d.%25H.%25M.%25S.%25Z", timefn)
	except:
		tfl = open(timefn,'wb')
		tfl.write('Error retrieving time; attempt failed twice.')
		tfl.close()
tfres = open(timefn,'rb')
tfres.read()
tfres.close()
log_add('Current time: '+ltime)

try:
	ak = open('./Archive/Meta/Revisions/Archive.eserdb/meta/conf','rb')
	ad = ak.read()
	ak.close()
except:
	ak = raw_input('access key? ');
	sk = raw_input('secret key? ');
	ax = open('./Archive/Meta/Revisions/Archive.eserdb/meta/conf','wb')
	ax.write(ak+"\n"+sk)
	ax.close()
	ad = ak
if len(ad) < 1:
	ak = raw_input('access key? ');
	sk = raw_input('secret key? ');
	ax = open('./Archive/Meta/Revisions/Archive.eserdb/meta/conf','wb')
	ax.write(ak+"\n"+sk)
	ax.close()
accesskey = open('./Archive/Meta/Revisions/Archive.eserdb/meta/conf', 'r').readlines()[0].strip()
secretkey = open('./Archive/Meta/Revisions/Archive.eserdb/meta/conf', 'r').readlines()[1].strip()

try:
	ak = open('./Archive/Meta/Revisions/Archive.eserdb/meta/passphrase','rb')
	ad = ak.read()
	ak.close()
except:
	sz = raw_input('passphrase? ');
	ax = open('./Archive/Meta/Revisions/Archive.eserdb/meta/passphrase','wb')
	ax.write(sz)
	ax.close()
	ad = sz
if len(ad) < 1:
	sz = raw_input('passphrase? ');
	ax = open('./Archive/Meta/Revisions/Archive.eserdb/meta/passphrase','wb')
	ax.write(sz)
	ax.close()

uu = ''
try:
	ak = open('./Archive/Meta/Revisions/Archive.eserdb/meta/uuid','rb')
	global uu
	uu = ak.read()
	ak.close()
except:
	global uu
	uu = uuid.uuid4().hex;
	ax = open('./Archive/Meta/Revisions/Archive.eserdb/meta/uuid','wb')
	print 'You have been assigned the following eser repository ID: '+uu
	ax.write(uu)
	ax.close()
if len(uu) < 1:
	global uu
	uu = uuid.uuid4().hex;
	ax = open('./Archive/Meta/Revisions/Archive.eserdb/meta/uuid','wb')
	print 'You have been assigned the following eser repository ID: '+uu
	ax.write(uu)
	ax.close()

ad = raw_input('Type y and press enter if you want to make a new snapshot, or press enter for a patch: ');
if ad == 'y':
	snapshot = True;
	run('rsync -av --progress --delete --checksum ./Archive/ ./Archive.stable/')
	run('rsync -av --progress --delete --checksum ./Archive/ ./Archive.stable/')
	run('tar -cvz --format pax -f Archive.snapshot.'+ltime+'.egz -C '+pwd.strip()+' ./Archive/')
	run('gpg --yes -c --cipher-algo AES256 --batch --passphrase-file ./Archive/Meta/Revisions/Archive.eserdb/meta/passphrase Archive.snapshot.'+ltime+'.egz')
	run('mv -v Archive.snapshot.'+ltime+'.egz.gpg Archive.snapshot.'+ltime+'.egze')
	run('rm -v Archive.snapshot.'+ltime+'.egz')
	#Starting upload to IA
	global uu
	identifier='Collistar_eser_db_'+uu+'_'+ltime
	log_add('Uploading...')
	time.sleep(0.1)
	title = "Colistarr Initiative: eser_db "+uu+" rev. "+ltime
	description = "Colistarr Initiative: eser_db "+uu+" rev. "+ltime
	keywords = ['Colistarr','Colistarr Initiative','eser','eser_db','archive','snapshot', title]                        
	barrelSize = int(os.path.getsize('Archive.snapshot.'+ltime+'.egze'))
	# http://curl.haxx.se/docs/manpage.html
	curl = ['curl', '--location', 
	  '--retry', '7',
	  '--retry-max-time', '0',
	  #'--max-time', '15', #FOR TESTING ONLY!!!! TODO remove
	  '--header', "'x-amz-auto-make-bucket:1'", # Creates the item automatically, need to give some time for the item to correctly be created on archive.org, or everything else will fail, showing "bucket not found" error
	  '--header', "'x-archive-queue-derive:0'",
	  '--header', "'x-archive-size-hint:%d'" % (os.path.getsize('Archive.snapshot.'+ltime+'.egze')), 
	  '--header', "'authorization: LOW %s:%s'" % (accesskey, secretkey),
	]
	curl += ['--header', "'x-archive-meta-mediatype:data'",
		  '--header', "'x-archive-meta-collection:%s'" % ('coalproject'),
		  '--header', "'x-archive-meta-title:%s'" % (title),
		  '--header', "'x-archive-meta-description:%s'" % (description),
		  '--header', "'x-archive-meta-subject:%s'" % ('; '.join(keywords)), # Keywords should be separated by ; but it doesn't matter much; the alternative is to set one per field with subject[0], subject[1], ...
		  '--header', "'x-archive-meta-mediatype:data'",
	]
	curl += ['--upload-file', "%s" % ('Archive.snapshot.'+ltime+'.egze'),
		  "http://s3.us.archive.org/" + identifier + '/' + 'Archive.snapshot.'+ltime+'.egze' # It could happen that the identifier is taken by another user; only wikiteam collection admins will be able to upload more files to it, curl will fail immediately and get a permissions error by s3.
	]
	curlline = ' '.join(curl)
	log_add('Executing curl request: ')
	errored = False
	uploadFetchResultB = run(curlline)[0]
	log_add('\n\ncurl request result:\n'+uploadFetchResultB+'\n\n')
	if not (errored or 'XML' in uploadFetchResultB or 'xml' in uploadFetchResultB or 'html' in uploadFetchResultB or 'HTML' in uploadFetchResultB):
	  log_add('Removing file\n')
	  try:
		  run('rm -v Archive.snapshot.'+ltime+'.egze')
	  except:
		  traceback.print_exc()
	else:
	  log_add('ERROR UPLOADING FILE. THIS IS NOT GOOD.')
	  sys.exit()
	errored = False
	time.sleep(10)
	c = c+1
	#Done upload to IA
	os.system('rm ./Archive/Meta/Revisions/Archive.eserdb/latest')
	run('echo "'+ltime+'" > ./Archive/Meta/Revisions/Archive.eserdb/latest')
else:
	snapshot = False;
	#http://unix.stackexchange.com/questions/25195/how-do-i-save-changed-files
	run('rsync --only-write-batch=Archive.patch.'+ltime+'.egp -av --progress --delete --checksum Archive/ Archive.stable/')
	run('gpg --yes -c --cipher-algo AES256 --batch --passphrase-file ./Archive/Meta/Revisions/Archive.eserdb/meta/passphrase Archive.patch.'+ltime+'.egp')
	run('mv -v Archive.patch.'+ltime+'.egp.gpg Archive.patch.'+ltime+'.egpe')
	run('rm -v Archive.patch.'+ltime+'.egp')
	ak = open('./Archive/Meta/Revisions/Archive.eserdb/latest','rb')
	latest = ak.read()
	#Starting upload to IA
	identifier='Collistar_eser_patch_'+uu+'_'+ltime
	log_add('Uploading: ' + item[0])
	time.sleep(0.1)
	global uu
	title = "Colistarr Initiative: eser_db patch "+uu+" rev. "+ltime+" for "+latest
	description = "Colistarr Initiative: eser_db patch "+uu+" rev. "+ltime+" for "+latest
	keywords = ['Colistarr','Colistarr Initiative','eser','eser_db','archive','snapshot', title]                        
	barrelSize = int(os.path.getsize('Archive.patch.'+ltime+'.egpe'))
	# http://curl.haxx.se/docs/manpage.html
	curl = ['curl', '--location', 
	  '--retry', '7',
	  '--retry-max-time', '0',
	  #'--max-time', '15', #FOR TESTING ONLY!!!! TODO remove
	  '--header', "'x-amz-auto-make-bucket:1'", # Creates the item automatically, need to give some time for the item to correctly be created on archive.org, or everything else will fail, showing "bucket not found" error
	  '--header', "'x-archive-queue-derive:0'",
	  '--header', "'x-archive-size-hint:%d'" % (os.path.getsize('Archive.patch.'+ltime+'.egpe')), 
	  '--header', "'authorization: LOW %s:%s'" % (accesskey, secretkey),
	]
	curl += ['--header', "'x-archive-meta-mediatype:data'",
		  '--header', "'x-archive-meta-collection:%s'" % ('coalproject'),
		  '--header', "'x-archive-meta-title:%s'" % (title),
		  '--header', "'x-archive-meta-description:%s'" % (description),
		  '--header', "'x-archive-meta-subject:%s'" % ('; '.join(keywords)), # Keywords should be separated by ; but it doesn't matter much; the alternative is to set one per field with subject[0], subject[1], ...
		  '--header', "'x-archive-meta-mediatype:data'",
	]
	curl += ['--upload-file', "%s" % ('Archive.patch.'+ltime+'.egpe'),
		  "http://s3.us.archive.org/" + identifier + '/' + 'Archive.patch.'+ltime+'.egpe' # It could happen that the identifier is taken by another user; only wikiteam collection admins will be able to upload more files to it, curl will fail immediately and get a permissions error by s3.
	]
	curlline = ' '.join(curl)
	log_add('Executing curl request: ')
	errored = False
	uploadFetchResultB = run(curlline)[0]
	log_add('\n\ncurl request result:\n'+uploadFetchResultB+'\n\n')
	if not (errored or 'XML' in uploadFetchResultB or 'xml' in uploadFetchResultB or 'html' in uploadFetchResultB or 'HTML' in uploadFetchResultB):
	  log_add('Removing file\n')
	  try:
		  run('rm -v Archive.patch.'+ltime+'.egpe')
	  except:
		  traceback.print_exc()
	else:
	  log_add('ERROR UPLOADING FILE. THIS IS NOT GOOD.')
	  sys.exit()
	errored = False
	time.sleep(10)
	c = c+1
	#Done upload to IA
	os.system('rm ./Archive/Meta/Revisions/Archive.eserdb/latestPatch')
	run('echo "'+ltime+'" > ./Archive/Meta/Revisions/Archive.eserdb/latestPatch')
#!/usr/bin/python
# -*- coding: utf-8 -*-
# Crystallise
# Version:
version = '1.01.5'
#, 2015.jan.11, based on WARCdealer 1.2 and pbz 9.
#IMPORTANT USAGE NOTES: 1) RUN AS ROOT 
# 2) MAKE SURE YOU HAVE ENOUGH DISK SPACE (4X INPUT), OR IT WILL DESTROY YOUR DATA
# 3) DON'T INTERRUPT IT, OR IT WILL DESTROY YOUR DATA
#
# Copyright (C) 2011-2012 WikiTeam
# Futuramerlin additions copyright 2013, 2014 Futuramerlin
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
# TODO: bug - upload may (partly) fail if two (small) files are sent to s3 without pause http://p.defau.lt/?puN_G_zKXbv1lz9TfSliPg http://archive.org/details/wiki-editionorg_w or something http://p.defau.lt/?udwrG7YQn4RK_1whl1XWRw http://archive.org/details/wiki-jutedreamhosterscom_lineageii_bestiary
# TODO: minor bug - don't overwrite existing files with same filename in the same identifier

# You need a file named config.txt with username, access key, secret key, and a title for the uploaded items, each in its own line.
userName=open('config.txt', 'r').readlines()[0].strip()
accesskey = open('config.txt', 'r').readlines()[1].strip()
secretkey = open('config.txt', 'r').readlines()[2].strip()
title = open('config.txt', 'r').readlines()[3].strip()
collection = 'coalproject' # Replace with "opensource" if you are not an admin of the collection
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
import glob
import random
import traceback
uuidG=str(uuid.uuid4())
pbzversion = '9 (compatible; generated by Crystallise '+version+')'
nowe = datetime.datetime.now()
timeRunning=nowe.strftime("%Y-%m-%d-%H-%M-%S-%f-%Z_E")
now = time.strftime("%Y.%m.%d.%H.%M.%S.%f.%z", time.gmtime())+"-"+uuid.uuid4().hex
year = time.strftime("%Y")
month = time.strftime("%m")
day = time.strftime("%d")
hour = time.strftime("%H")
minute = time.strftime("%M")
second = time.strftime("%S")
action = 'save';
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
	global timeRunning
	f = open('log-'+timeRunning+'.log', 'a')
	f.write(text+"\n")
	f.close()
def run(command):
	log_add(command)
	global errored
	commandResult = ''
	try:
		commandRes=check_output(command, shell=True, stderr=subprocess.STDOUT)
		commandResult = "Running command: \n\n" + command + "\n\n\n\n" + commandRes + "\n\n\n\n"
	except Exception, e:
		commandRes=''
		try:
			log_add(traceback.format_exc())
			log_add(sys.exc_info()[0])
			commandResult = "Running command: \n\n" + command + "\n\n\n\n" + commandResult + str(e) + "See above for error details. \n\n\n\nError encountered while running command. This is probably not a big deal.\n\n"
		except Exception, e:
			#based on http://stackoverflow.com/questions/3702675/print-the-full-traceback-in-python-without-halting-the-program
			log_add(traceback.format_exc())
			log_add(sys.exc_info()[0])
			commandResult = "\n\n\n\nError encountered while running command: \n\n" + command + "\n\n\n\nThis is probably not a big deal. Possibly the command line was incorrectly structured?\n\n"
		errored=True
	log_add('Command result: ' +commandResult)
	log_add("\n\n")
	log_add('Command res: ' +commandRes)
	return [commandResult,commandRes]
#Is there anything to do? If so do it.
#help from http://stackoverflow.com/questions/2632205/count-the-number-of-files-in-a-directory-using-python
run('du -hs')
td = raw_input("Still do it? [y]es/[n]o ")
if td == 'y':
	log_add(len(glob.glob('*')))
	if len(glob.glob('*')) > 3:
		ccgo = os.path.expanduser("/Volumes/disk2s1/.pbzc")
		ad = ''
		try:
			ak = open(ccgo,'rb')
			ad = ak.read()
			ak.close()
		except:
			pass
		if len(ad) < 1:
			ad = raw_input('Give a name / description to this computer: ');
			run('rm -v /Volumes/disk2s1/.pbzc 2> /dev/null')
			ax = open(ccgo,'wb')
			ax.write(ad)
			ax.close()
		ccgo = os.path.expanduser("/Volumes/disk2s1/.pbziid")
		ad = ''
		try:
			ak = open(ccgo,'rb')
			ad = ak.read()
			ak.close()
		except:
			pass
		if len(ad) < 1:
			ad = uuid.uuid4().hex;
			log_add('You have been assigned the following pbz installation ID: '+ad)
			run('rm -v /Volumes/disk2s1/.pbziid 2> /dev/null')
			ax = open(ccgo,'wb')
			ax.write(ad)
			ax.close()
		inf = '.'
		def filo():
			ad = '.'
			opr = ad+"/.tmp.Packed-"+now
			opd = ad+"/Packed-"+now
			log_add('Outputting to: '+opd)
			return opr
		def enci():
			cfgp = os.path.expanduser("/Volumes/disk2s1/.pbz")
			ad = ''
			try:
				ak = open(cfgp,'rb')
				ad = ak.read()
				ak.close()
			except:
				pass
			if len(ad) < 1:
				ad = raw_input('confirm authkey? ');
				run('rm -v /Volumes/disk2s1/.pbz 2> /dev/null')
				ax = open(cfgp,'wb')
				ax.write(ad)
				ax.close()
		def pack(of):
			run('mkdir '+inf+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/')
			run('mkdir '+inf+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/.config-'+now+'.AddedToPackedDirOnPack.pcb/')
			timefile = urllib.URLopener()
			timefn = os.path.expanduser(inf).replace('\\','')+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/.Packed-'+now+'.AddedToPackedDirOnPack.ptd'
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
			vfl = open(os.path.expanduser(inf).replace('\\','')+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/.Packed-'+now+'.AddedToPackedDirOnPack.pbzversion','wb')
			vfl.write(pbzversion)
			vfl.close()
			userpath = os.path.expanduser("~")
			log_add('Directories should not be copied now')
			run('cp -v '+userpath+'/.pbz* '+inf+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/.config-'+now+'.AddedToPackedDirOnPack.pcb/')
			log_add('Directories should be copied now')
			run('cp -Rv '+inf+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/ '+of+'.pmb/')
			ofpmbpath = of+'.pmb'
			tarc = 'tar -cvj --format pax -f '+of+'.pmbz -C '+os.path.dirname(ofpmbpath)+' '+os.path.basename(ofpmbpath)+'/'
			log_add('Running: '+tarc)
			run(tarc)
			run('rm -rv '+of+'.pmb/')
			run('hashdeep -o f -c sha1 -r '+inf+' > '+inf+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/.Packed-'+now+'.AddedToPackedDirOnPack.pdx')
			run('cp -v '+inf+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/.Packed-'+now+'.AddedToPackedDirOnPack.pdx '+of+'.pdx')
			run('bzip2 '+of+'.pdx')
			run('tar -cvj --format pax -f '+of+'.pbz '+inf)
		def encrypt(of):
			run('gpg --yes -c --cipher-algo AES256 --batch --passphrase-file /Volumes/disk2s1/.pbz '+of+'.pbz')
			run('mv -v '+of+'.pbz.gpg '+of+'.pbze')
			run('rm -v '+of+'.pbz')
			run('gpg --yes -c --cipher-algo AES256 --batch --passphrase-file /Volumes/disk2s1/.pbz '+of+'.pmbz')
			run('mv -v '+of+'.pmbz.gpg '+of+'.pmbze')
			run('rm -v '+of+'.pmbz')
			run('gpg --yes -c --cipher-algo AES256 --batch --passphrase-file /Volumes/disk2s1/.pbz '+of+'.pdx.bz2')
			run('mv -v '+of+'.pdx.bz2.gpg '+of+'.pdxe')
			run('rm -v '+of+'.pdx.bz2')
		def finish():
			ad = '.'
			log_add('Copying indices...')
			global timeRunning;
			global title;
			global uuidG;
			global now;
			global year
			global month
			global day
			global hour
			global minute
			global second
			run('mkdir /Volumes/disk2s1/FuturamerlinMultimediaArchive/Nonfiction/Data/ColistarrCollectionIndex/\('+year+'-'+month+'-'+day+'-'+hour+'-'+minute+'-'+second+'\)\ Crystallise_ColistarrPack_'+title+'_' + uuidG +'.' +timeRunning+'/');
			run('cp -Rv '+inf+'/.pbz-meta-'+now+'.AddedToPackedDirOnPack.pmb/ /Volumes/disk2s1/FuturamerlinMultimediaArchive/Nonfiction/Data/ColistarrCollectionIndex/\('+year+'-'+month+'-'+day+'-'+hour+'-'+minute+'-'+second+'\)\ Crystallise_ColistarrPack_'+title+'_' + uuidG +'.' +timeRunning+'/Packed-'+now+'.pmb/');
			run('mv -v /Volumes/disk2s1/FuturamerlinMultimediaArchive/Nonfiction/Data/ColistarrCollectionIndex/\('+year+'-'+month+'-'+day+'-'+hour+'-'+minute+'-'+second+'\)\ Crystallise_ColistarrPack_'+title+'_' + uuidG +'.' +timeRunning+'/Packed-'+now+'.pmb/.Packed-'+now+'.AddedToPackedDirOnPack.pdx /Volumes/disk2s1/FuturamerlinMultimediaArchive/Nonfiction/Data/ColistarrCollectionIndex/\('+year+'-'+month+'-'+day+'-'+hour+'-'+minute+'-'+second+'\)\ Crystallise_ColistarrPack_'+title+'_' + uuidG +'.' +timeRunning+'/Packed-'+now+'.pdx');
			log_add('Note that some warnings from mv that look like "mv: rename /blahblahblah to /blahblahblah: No such file or directory" are ok here.')
			run('mv -v '+ad+'/.tmp.Packed-'+now+'.pbz '+ad+'/Packed-'+now+'.pbz')
			run('mv -v '+ad+'/.tmp.Packed-'+now+'.pdx '+ad+'/Packed-'+now+'.pdx')
			run('mv -v '+ad+'/.tmp.Packed-'+now+'.pmbz '+ad+'/Packed-'+now+'.pmbz')
			run('mv -v '+ad+'/.tmp.Packed-'+now+'.pbze '+ad+'/Packed-'+now+'.pbze')
			run('mv -v '+ad+'/.tmp.Packed-'+now+'.pdxe '+ad+'/Packed-'+now+'.pdxe')
			run('mv -v '+ad+'/.tmp.Packed-'+now+'.pmbze '+ad+'/Packed-'+now+'.pmbze')
			log_add('You shouldn\'t see any more warnings from mv now.')
			log_add('Done!')
		#od = raw_input('new output directory? (have 30gb free space for it there) ')
		od = ''
		#od = os.path.expanduser(od)
		cfgo = os.path.expanduser("/Volumes/disk2s1/.pbzl")
		run('rm -v /Volumes/disk2s1/.pbzl 2> /dev/null')
		ax = open(cfgo,'wb')
		ax.write(od)
		ax.close()
		of = filo()
		enci()
		pack(of)
		encrypt(of)
		finish()

		def shellesc(s):
			return s.replace("'", "%27").replace(' ','%20').replace('<','%3C').replace('>','%3E').replace('[','%5B').replace(']','%5D').replace('(','%28').replace(')','%29').replace(';','%3B').replace("\x00",'%00').replace("\x0c",'%0C').replace("\x0b",'%0B').replace("\x08",'%08').replace("\x03",'%03')

		convertlang = {'ar': 'Arabic', 'de': 'German', 'en': 'English', 'es': 'Spanish', 'fr': 'French', 'it': 'Italian', 'ja': 'Japanese', 'nl': 'Dutch', 'pl': 'Polish', 'pt': 'Portuguese', 'ru': 'Russian'}
		errored = False
		def log(wiki, dump, msg):
			global timeRunning
			f = open('uploader-'+timeRunning+'.log', 'a')
			f.write('\n%s;%s;%s' % (wiki, dump, msg))
			f.close()
		timeFetchResult=run('bash -c \'wget --no-check-certificate --warc-file='+timeRunning+'.Now -O now.txt "http://www.timeapi.org/utc/now?\\Y-\\m-\\d-\\H-\\M-\\S-\\6N-\\z"\'')[0]

		log_add('Running from: '+run('pwd')[0])
		with open ("now.txt", "r") as timeFile:
			timeRemote=timeFile.read()
		#log_add("\Current time fetch output: \n"+timeFetchResult+"\n\n")
		#log_add("\nCurrent time retrieved remotely: \n"+timeRemote+"\n\n")
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
						log_add('Filename being checked: '+f)
						if (f.startswith('Packed')):
							log_add('Filename matched; is appended: '+f)
							dumps.append(f)
					break
			log_add(dumps)
			c = 0
			for dump in dumps:
				dumpid='Crystallise_ColistarrPack_'+title+'_' + uuidG +'.' +timeRunning
				log_add("#"*73)
				log_add('ATTEMPTING TO UPLOAD PACK: ' + dump)
				log_add('PACK ID: ' + dumpid)
				log_add("#"*73)
				time.sleep(0.1)
				wikititle = "Colistarr Initiative pack. Documents in set labeled: "+ title + ', ID: ' + uuidG
				wikidesc = "Colistarr Initiative pack. Documents in set labeled: "+ title + ', ID ' + uuidG+'. '+title+'_' + uuidG +'.' +timeRunning
				wikikeys = ['Colistarr','Colistarr Initiative','Crystallise', title, title+'_' + uuidG +'.' +timeRunning]                        
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
					curl += ['--header', "'x-archive-meta-mediatype:data'",
						'--header', "'x-archive-meta-collection:%s'" % (collection),
						'--header', "'x-archive-meta-title:%s'" % (wikititle),
						'--header', "'x-archive-meta-description:%s'" % (wikidesc),
						'--header', "'x-archive-meta-subject:%s'" % ('; '.join(wikikeys)), # Keywords should be separated by ; but it doesn't matter much; the alternative is to set one per field with subject[0], subject[1], ...
						'--header', "'x-archive-meta-mediatype:data'",

					]
		
				curl += ['--upload-file', "%s" % (dump),
						"http://s3.us.archive.org/" + dumpid[:99] + '/' + dump # It could happen that the identifier is taken by another user; only wikiteam collection admins will be able to upload more files to it, curl will fail immediately and get a permissions error by s3.
				]
				curlline = ' '.join(curl)
				log_add('Executing curl request: ')
				log_add(curlline+'\n')
				if errored:
					sys.exit()
				errored = False
				uploadFetchResultB = run(curlline)[0]
				log_add('\n\ncurl request result:\n'+uploadFetchResultB+'\n\n')
				c += 1
				if errored:
					sys.exit()
				if not (errored or 'XML' in uploadFetchResultB or 'xml' in uploadFetchResultB or 'html' in uploadFetchResultB or 'HTML' in uploadFetchResultB):
					run('rm '+dump)
					log_add('Removing file: '+dump+'\n')
					statDuro = True
				else:
					log_add('ERROR UPLOADING BARREL. THIS IS NOT GOOD.')
					sys.exit()
				log_add('Logging added item: ' + 'https://archive.org/details/' + dumpid + '\n\n\n\n\n')

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
			global year
			global month
			global day
			global hour
			global minute
			global second
			iId=1
			log_add('\n\nUploading barrel data back to base.\n\n');
			upload(wikis)
			if errored:
					sys.exit()
			if not errored:
				log_add('\n\nDone uploading; removing inputs.\n\n')
				for root, dirs, files in os.walk('.'):
					for file in files:
						if not ((os.path.join(root,file).startswith('./log-') and os.path.join(root,file).endswith('-_E.log')) or (os.path.join(root,file) == './crystallise.py') or (os.path.join(root,file)=='./config.txt') or (os.path.join(root,file)=='./now.txt')):
							log_add('\n\nRemoving file: '+file+'\n\n')
							run('rm -rv \''+os.path.join(root,file).replace('\'','\'\\\'\'')+'\'')
			#help from http://unix.stackexchange.com/questions/46322/how-can-i-recursively-delete-empty-directories-in-my-home-directory
			#help from http://askubuntu.com/questions/153770/how-to-have-find-not-return-the-current-directory
			run('find `pwd` -type d -mindepth 1 -not -name \'.pbz-meta-*\' -exec rm -rv {} + 2>/dev/null')
			log_add('Sleeping 10 seconds')
			run('mv ./log-'+timeRunning+'.log /Volumes/disk2s1/FuturamerlinMultimediaArchive/Nonfiction/Data/ColistarrCollectionIndex/\('+year+'-'+month+'-'+day+'-'+hour+'-'+minute+'-'+second+'\)\ Crystallise_ColistarrPack_'+title+'_' + uuidG +'.' +timeRunning+'/')
			time.sleep(10)
		main()
log_add('Done.')
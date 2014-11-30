#!/usr/bin/python
# -*- coding: utf-8 -*-
# sser
# (the SnapShottER)
# Version 0.12, 2014.nov.30.
#
# Copyright (C) 2011-2012 WikiTeam
# Additions copyright 2013, 2014 Futuramerlin
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

# 1. ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/localTime <- {time} <- now
# 2. {records} <- list of everything and its shasum --algorithm 512 hash.
# 3. foreach {records} as {item}:
#   I. If ../Archive.sserdb/hashesdb/{hash}.exists:
#     A. break; # file already in repo
#   II. Else: #new file since last snapshot
#     A. ../Archive.sserdb/encdb/enctmp <- {item}.aes256()
# 	  B. ../Archive.sserdb/hashesdb/{hash} <- {enctmp.sha512()}
#     C. Hardlink ../Archive.sserdb/encdb/{enctmp.sha512()} to ../Archive.sserdb/encdb/enctmp
#     D. Upload ../Archive.sserdb/encdb/{enctmp.sha512()} to ia:Collistar_sser_db_{../Archive.sserdb/UUID}
#     E. Delete ../Archive.sserdb/encdb/enctmp
#     F. ../Archive.sserdb/ehdb/{enctmp.sha512()} <- {hash}
# 4. Delete ../Archive.sserdb/encdb/
# 5. Clone directory tree to ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/d/
# 6. foreach {records} as {item}:
#   I. {item.name} <- "http://archive.org/download/Collistar_sser_pack_{../Archive.sserdb/uuid}_{{../Archive.sserdb/latest}++}/{enctmp.sha512()}"
# 7. Hardlink ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/idx/ehdb/ to ../Archive.sserdb/ehdb/
# 8. Hardlink ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/idx/hashesdb/ to ../Archive.sserdb/hashesdb/
# 9. ../.tmp.{../Archive.sserdb/uuid}.{time} <- ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/.pax().bzip2().aes256()
# 10. Upload ../.tmp.{../Archive.sserdb/uuid}.{time} to ia:Collistar_sser_pack_{../Archive.sserdb/uuid}_{{../Archive.sserdb/latest}++}
# 11. ../.tmp.{../Archive.sserdb/uuid}.{time}
# 12. Move ../.tmp.{../Archive.sserdb/uuid}.{time} to ./Meta/Revisions/{{../Archive.sserdb/latest}++}.sserrev
# 13. ../Archive.sserdb/latest++;

import os
import uuid
import time
import urllib
import traceback
import subprocess

print 'Note that this app should have at LEAST 2x the size of the biggest file of free space.'
ad = ''
try:
	ak = open('../Archive.sserdb/meta/latest','rb')
	ad = ak.read()
	ak.close()
except:
	pass
if len(ad) < 1:
	os.system('mkdir ../Archive.sserdb')
	os.system('mkdir ../Archive.sserdb/meta/')
	os.system('mkdir ../Archive.sserdb/snapshots/')
	os.system('mkdir ../Archive.sserdb/snapshots/disabled/')
	os.system('mkdir ../Archive.sserdb/ehdb/')
	os.system('mkdir ../Archive.sserdb/encdb/')
	os.system('mkdir ../Archive.sserdb/hashesdb/')
	os.system('mkdir ./Meta/')
	os.system('mkdir ./Meta/Revisions/')
	ad = '0';
	print 'Initializing new sser repository'
	ax = open('../Archive.sserdb/meta/latest','wb')
	ax.write(ad)
	ax.close()
	ad = uuid.uuid4().hex;
	ax = open('../Archive.sserdb/meta/uuid','wb')
	ax.write(ad)
	ax.close()
	print 'You have been assigned the following sser repository ID: '+ad
	ak = raw_input('access key? ');
	sk = raw_input('secret key? ');
	pp = raw_input('passphrase? ');
	ax = open('../Archive.sserdb/meta/conf','wb')
	ax.write(ak+"\n"+sk)
	ax.close()
	ax = open('../Archive.sserdb/meta/passphrase','wb')
	ax.write(pp)
	ax.close()

ltime = time.strftime("%Y.%m.%d.%H.%M.%S.%f.%z", time.gmtime())		

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
    f = open('log-'+ltime+'.log', 'a')
    f.write(text+"\n")
    f.close()
def run(command):
    log_add("Running: "+command)
    global errored
    commandResult = ''
    try:
        commandRes=check_output(command, shell=True, stderr=subprocess.STDOUT)
        commandResult = "Running command: \n" + command + "\n" + commandRes + "\n"
    except Exception, e:
    	# http://stackoverflow.com/questions/9555133/e-printstacktrace-equivalent-in-python
    	traceback.print_exc()
        commandRes=''
        try:
            commandResult = "Running command: \n" + command + "\n" + commandResult + str(e.output) + "\nError encountered while running command. This is probably not a big deal."
        except Exception, e:
            commandResult = "Error encountered while running command: \n" + command + "\nThis is probably not a big deal. Possibly the command line was incorrectly structured?"
        errored=True
    log_add('Command result: '+commandResult+'\n')
    log_add('Command output: '+commandRes+'\n')
    return [commandResult,commandRes]
#Done command definitions

previousRevision=int(open('../Archive.sserdb/meta/latest', 'r').readlines()[0])
thisRevision = str(previousRevision + 1)
uuid = open('../Archive.sserdb/meta/uuid', 'r').readlines()[0]
accesskey = open('../Archive.sserdb/meta/conf', 'r').readlines()[0].strip()
secretkey = open('../Archive.sserdb/meta/conf', 'r').readlines()[1].strip()
passphrase = open('../Archive.sserdb/meta/passphrase', 'r').readlines()[0].strip()
collection = 'coalproject'

try:
	run('mv -v ../Archive.sserdb/snapshots/'+thisRevision+' ../Archive.sserdb/snapshots/disabled/'+thisRevision+'_'+ltime)
except:
	pass
errored = False #doesn't matter if there were errors before here
run('mkdir -v ../Archive.sserdb/snapshots/'+thisRevision)
run('mkdir -v ../Archive.sserdb/snapshots/'+thisRevision+'/d/')

#Start getting time
run('pwd')
run('echo "'+ltime+'" > ../Archive.sserdb/snapshots/'+thisRevision+'/localTime')
timefile = urllib.URLopener()
timefn = '../Archive.sserdb/snapshots/'+thisRevision+'/remoteTime'
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
#Done getting time

records = []
for dirpath, dirs, files in os.walk('Test'):
	with open(os.path.join(dirpath, filename)) as f:
		records.append([f,os.popen('shasum --algorithm 512 '+f),filename])
c = 0
for item in records:
	if os.path.isfile('../Archive.sserdb/hashesdb/'+item[1]):
		break; # file already in repo
	else: # new file since last snapshot
		run('cp -v '+item[0]+' ../Archive.sserdb/encdb/enctmp')
		run('gpg --yes -c --cipher-algo AES256 --batch --passphrase-file ../Archive.sserdb/meta/passphrase ../Archive.sserdb/encdb/enctmp')
		run('rm -v ../Archive.sserdb/encdb/enctmp')
		run('mv -v ../Archive.sserdb/encdb/enctmp.gpg ../Archive.sserdb/encdb/enctmp')
		encHash = os.popen('shasum --algorithm 512 ../Archive.sserdb/encdb/enctmp')
		run('echo "'+encHash+'" > ../Archive.sserdb/hashesdb/'+item[1])
		run('ln ../Archive.sserdb/encdb/'+encHash+' ../Archive.sserdb/encdb/enctmp')

		#Starting upload to IA
		identifier='Collistar_sser_db_'+uuid+'_'+thisRevision
        log_add('Uploading: ' + item[0])
        time.sleep(0.1)
        title = "Colistarr Initiative: sser_db "+uuid+" rev. "+thisRevision
        description = "Colistarr Initiative: sser_db "+uuid+" rev. "+thisRevision
        keywords = ['Colistarr','Colistarr Initiative','sser','sser_db','archive','snapshot', title]                        
    	barrelSize = int(os.path.getsize(item[0]))
        curl = ['curl', '--location', 
        	'--retry', '999',
        	'--retry-max-time', '0',
            '--header', "'x-amz-auto-make-bucket:1'", # Creates the item automatically, need to give some time for the item to correctly be created on archive.org, or everything else will fail, showing "bucket not found" error
            '--header', "'x-archive-queue-derive:0'",
            '--header', "'x-archive-size-hint:%d'" % (os.path.getsize(item[0])), 
            '--header', "'authorization: LOW %s:%s'" % (accesskey, secretkey),
        ]
        if c == 0:
            curl += ['--header', "'x-archive-meta-mediatype:data'",
                '--header', "'x-archive-meta-collection:%s'" % (collection),
                '--header', "'x-archive-meta-title:%s'" % (title),
                '--header', "'x-archive-meta-description:%s'" % (description),
                '--header', "'x-archive-meta-subject:%s'" % ('; '.join(keywordvision
keywords = ['Colistarr','Colistarr Initiative','sser','sser_snapshot','archive','snapshot', uuid, uuid+" rev. "+thisRevision, title]                   '--header', "'x-archive-meta-mediatype:data'",
            ]
        curl += ['--upload-file', "%s" % (item[0]),
                "http://s3.us.archive.org/" + identifier + '/' + item[2] # It could happen that the identifier is taken by another user; only wikiteam collection admins will be able to upload more files to it, curl will fail immediately and get a permissions error by s3.
        ]
        curlline = ' '.join(curl)
        log_add('Executing curl request: ')
        errored = False
        uploadFetchResultB = run(curlline)[0]
        log_add('\n\ncurl request result:\n'+uploadFetchResultB+'\n\n')
        c += 1
        if not (errored or 'XML' in uploadFetchResultB or 'xml' in uploadFetchResultB or 'html' in uploadFetchResultB or 'HTML' in uploadFetchResultB):
            log_add('Removing enctmp file\n')
            run('rm -v ../Archive.sserdb/encdb/enctmp')
        else:
            log_add('ERROR UPLOADING FILE. THIS IS NOT GOOD.')
            sys.exit()
        errored = False
        c = c+1
        #Done upload to IA
        
        run('echo "'+hash+'" > ../Archive.sserdb/ehdb/'+encHash)
log_add(run('rm -rfv ../Archive.sserdb/encdb/')[0])
# http://www.linuxquestions.org/questions/linux-general-1/how-to-copy-a-directory-tree-withoutitem in recordsles-in-it-10797/
run('find . -type d -exec mkdir -p ../Archive.sserdb/snapshots/'+thisRevision+'/d/{} \;')
# 6. foreach {records} as {item}:
#   I. {item.name} <- "http://archive.org/download/Collistar_sser_pack_{../Archive.sserdb/uuid}_{{../Archive.sserdb/latest}++}/{enctmp.sha512()}"
# 7. Hardlink ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/idx/ehdb/ to ../Archive.sserdb/ehdb/
# 8. Hardlink ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/idx/hashesdb/ to ../Archlive.sserdb/hashesdb/
# 9. ../.tmp.{../Archive.sserdb/uuid}.{time} <- ../Archive.sserdb/snapshots/{{../Archive.sserdb/latest}++}/.pax().bzip2().aes256()
# 10. Upload ../.tmp.{../Archive.sserdb/uuid}ltime)
run('rm -v ../Archive.sserdb/.tmp.'+uuid+'.'+ltime)
run('mv -v ../Archive.sserdb/.tmp.'+uuid+'.'+lp.{../Archive.sserdb/uuid}.{time}
# 12. Move ../.tmp.{../Archive.sserdb/uuid}.{time} to ./Meta/Revisions/{{../Archive.sserdb/latest}++}.sserrev
# 13. ../Archiv./Meta/Revisions/'+thisRevision+'.sserrev'test++;
for records as item:
	run('echo "http://archive.org/download/Collistar_sser_db_'+uuid+'_'+thisRevision+'/'+encHash+'" > ../Archive.sserdb/snapshots/'+thisRevision+'/d/'+item[0])
run('ln ../Archive.sserdb/snapshots/'+thisRevision+'/idx/ehdb/ ../Archive.sserdb/ehdb/')
run('ln ../Archive.sserdb/snapshots/'+thisRevision+'/idx/hashesdb/ ../Archive.sserdb/ha'./Meta/Revisions/'+thisRevision+'.sserrev'db/')
run('ln ../Archive.sserdb/snapshots/'+thisRevision+'/meta/ ../Archive.sserdb/meta/')
run('tar -cvj --format pax -f ../Archive.sserdb/.tmp.'+uuid+'.'+time+' -C .. '+'../Archive.sserdb/snapshots/'+thisRevision+'/')
run('gpg --yes -c --cipher-algo AES256 --batch --passphrase-file ../Archive.sserdb/meta/passphrase ../Archive.sserdb/.tmp.'+uuid+'.'+time)
run('rm -v ../Archive.sserdb/.tmp.'+uui'./Meta/Revisions/'+thisRevision+'.sserrev')), 
	'--header', "'authorization: LOW %s:%s'" % (accesskey, secretkey),
]
curl += ['--header', "'x-archive-meta-mediatype:data'",
	'--header', "'x-archive-meta-collection:%s'" % (collection),
	'--header', "'x-archive-meta-title:%s'" % (title),
	'--header', "'x-archive-meta-description:%s'" % (description),
	'--header', "'x-archive-meta-subject:%s'" % ('; '.join(keywordvision
keywords = ['Colistarr','Colistarr Initiative','sser','sser_snapshot','archive','snapshot', uuid, uuid+" rev. "+thisRevision, title]    '--header', "'x-archive-meta-mediatype:data'",
]
curl += ['--upload-file', "%s" % ('./Meta/Revisions/'+thisRevision+'.sserrev'),
		"http://s3.us.archive.org/" + identifier + '/' + thisRevision+'.sserrev' # It could happen that the identifier is taken by another user; only wikiteam collection admins will be able to upload more files to it, curl will fail immediately and get a permissions error by s3.
	'--header', "'x-archive-queue-derive:0'",
	'--header', "'x-archive-size-hint:%d'" % (os.path.getsize(dump)), 
	'--header', "'authorization: LOW %s:%s'" % (accesskey, secretkey),
]
if c == 0:
	curl += ['--header', "'x-archive-meta-mediatype:data'",
		'--header', "'x-archive-meta-collection:%s'" % (collection),
		'--header', "'x-archive-meta-title:%s'" % (title),
		'--header', "'x-archive-meta-description:%s'" % (description),
		'--header', "'x-archive-meta-subject:%s'" % ('; '.join(wikikeys)), # Keywords should be separated by 
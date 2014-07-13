import re
import os
import sys
import time
import base64
import hashlib
import subprocess
from time import sleep, gmtime, strftime
#TODO: Fix: New records are not correctly updated in the db, so files from previous snapshots are getting restored in place of the copy of the file from the newer snapshot.
#TODO: Fix: Truncated tar archive error; checksum mismatch on restore. See "checksum mismatch info folder created 13 June 2014 a.mn.".
#from http://stackoverflow.com/questions/35817/how-to-escape-os-system-calls-in-python
def shellquote(s):
	return "'" + s.replace("'", "'\\''") + "'"
def checkf(filename,needle):
	datafile = file(filename)
	found = False #this isn't really necessary 
	for line in datafile:
		if needle in line:
			#found = True #not necessary 
			return True
	return False #because you finished the search without finding anything
#help from http://stackoverflow.com/questions/287871/print-in-terminal-with-colors-using-python
hp = os.path.expanduser("~/.bash_profile")
# os.system('mv '+hp+' .ember.punkdb/.profile.punkbpbk')
# os.system('cp .ember.punkdb/.profile.punkbpbk '+hp)
if not os.path.exists(hp):
	w = open(hp, 'wb')
	w.write('export CLICOLOR=1\n')
	w.close()
# else:
# 	if not checkf(hp,'export CLICOLOR=1'):
# 		w = open(hp, 'ab')
# 		w.write('export CLICOLOR=1\n')
# 		w.close()

print '\033[91m'+'IMPORTANT WARNING: If you have existing files or directories with name extension beginning with "punk" they may be overwritten!'+'\033[0m'
running = True
while running == True:
	action = raw_input('action (save/restore/nothing)? ');

	if action.lower().strip() == 'nothing' or action.lower().strip() == 'quit' or action.lower().strip() == 'close':
		sys.exit()

	if action.lower().strip() == 'save':
		def run():
			if not os.path.exists('.ember.punkdb/.snapshots.punkset'):
				os.makedirs('.ember.punkdb/.snapshots.punkset')
			tempDir = raw_input('where to save big temporary files (omit trailing /) (default: here)? ');
			print 'Saving checksums of directory state...'
			# help from http://www.linuxquestions.org/questions/linux-software-2/bash-how-to-redirect-output-to-file-and-still-have-it-on-screen-412611/
			now = strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())
			tnosw = now
			os.system('md5deep -r . | tee .ember.punkdb/.snapshots.punkset/.snapshot.'+now+'.punkcsum')
			print 'Finished saving checksums of directory state.'
			if len(tempDir) < 1:
				tempDir = '.'
			else:
				if not os.path.exists(tempDir+'/'+'.ember.punkdb'):
					os.makedirs(tempDir+'/'+'.ember.punkdb')
			ad = ''
			try:
				ak = open('.ember.punkdb/.this.punkak','rb')
				ad = ak.read()
			except:
				pass
			if len(ad) < 1:
				ad = raw_input('authkey? ');
				ax = open('.ember.punkdb/.this.punkak','wb')
				ax.write(ad)

			sn = ''
			try:
				snl = open('.ember.punkdb/.this.punksn','rb')
				sn = snl.read()
			except:
				pass
			if len(sn) < 1:
				sn = raw_input('collection name? ');
				snx = open('.ember.punkdb/.this.punksn','wb')
				snx.write(sn)
	
			un = ''
			try:
				unl = open('.ember.punkdb/.this.punkun','rb')
				un = unl.read()
			except:
				pass
			if len(un) < 1:
				un = raw_input('user name? ');
				unx = open('.ember.punkdb/.this.punkun','wb')
				unx.write(un)

			# based on http://stackoverflow.com/questions/519633/lazy-method-for-reading-big-file-in-python
			def read_in_chunks(file_object, chunk_size=4194304):
				"""Lazy function (generator) to read a file piece by piece.
				Default chunk size: 1k."""
				while True:
					data = file_object.read(chunk_size)
					if not data:
						break
					yield data

			#based on http://stackoverflow.com/questions/4940032/search-for-string-in-txt-file-python
			def check(filename,needle,filter):
				os.system('grep '+filter+' '+filename+' > ./.ember.punkdb/.temp.punkck')
				datafile = file('./.ember.punkdb/.temp.punkck')
				found = False #this isn't really necessary 
				for line in datafile:
					if needle in line:
						#found = True #not necessary 
						return True
				return False #because you finished the search without finding anything
			
			def runCurlSendOp(ccmd):
				#help from http://stackoverflow.com/questions/7575284/check-output-from-calledprocesserror
				try:
					res = subprocess.check_output(ccmd, shell = True).strip()
				except subprocess.CalledProcessError:
					res = ''
				return res

			def sendChunkOp(tempfilename,name,tdl,un='',sn=''):
# 				print 'Running sendChunkOp'
				r = open(tempfilename,'rb')
				piece = r.read()
				uninsert = ''
				if len(un) == 0:
					 uninsert = ' -F "punkUser='+un+'"'
				#help from http://effbot.org/pyfaq/what-are-the-rules-for-local-and-global-variables-in-python.htm and http://stackoverflow.com/questions/423379/using-global-variables-in-a-function-other-than-the-one-that-created-them
				csum = '|'+str(len(piece))+'|'+hashlib.md5(piece).hexdigest()+'|'+hashlib.sha1(piece).hexdigest()+'|'+hashlib.sha512(piece).hexdigest()
				#help from http://unix.stackexchange.com/questions/94604/does-curl-have-a-timeout
				ccmd = 'curl --connect-timeout 30 -m 1024 -F "authorizationKey='+ad+'" -F "handler=1"'+uninsert+' -F "punkCollection='+sn+'" -F "handlerNeeded=PunkRecordIntake" -F "uploadedfile=@'+tempfilename+'" http://localhost:8888/d/r/active.php'
				#print ccmd
				res = ''
				while res == '':
					res = runCurlSendOp(ccmd)
				#help from http://stackoverflow.com/questions/1493007/identical-string-return-false-with-in-python-why
# 				print 'Csum:' +repr(csum.strip())
# 				print 'resf:' +repr(res[res.find('|'):].strip())
# 				print 'test:' +res[res.find('|'):].strip() == csum.strip()
				bleh = res[res.find('|'):].strip()
				blah = csum.strip()
# 				blah = ''
				blr = (bleh!=blah)
# 				print 'bleh:'+repr(bleh)
# 				print 'blah:'+repr(blah)
# 				print 'blr:'+str(blr)
				if blr:
					print 'res:'+repr(res)
					print 'bleh:'+repr(bleh)
					print 'blah:'+repr(blah)
					print 'blr:'+str(blr)
					res = ''
				return res
			def sendChunk(tempfilename,name,tdl,un='',sn=''):
				csum = ''
 				print 'Sending chunk'
				res = ''
				tca = 0
				tcb = 0
# 				print 'Csum: '+csum
# 				print 'Testing step 1 loop'
				while (not re.match('[0-9]+\|',res.strip())) and (tca<10):
# 					print 'Step 1 loop matched'
					#help from http://stackoverflow.com/questions/14011149/python-variable-increment-while-loop
					tca+=1
# 					print tca
# 					print 'Csum: '+csum
					#help from http://stackoverflow.com/questions/4967580/pythonhow-to-get-the-size-of-a-string-before-writing-to-a-file
					while (len(res) == 0) and (tcb<10):
# 						print 'Step 2 loop matched'
						tcb+=1
						res = sendChunkOp(tempfilename,name,tdl,un='',sn='')
# 						print res
# 						return res
						if len(res) == 0:
							print "Failed sending; retrying "+str(30-tcb)+" more times."
							print "Sleeping "+str((8*(tcb-1)))+"sec."
							sleep(8*(tcb-1))
					if not re.match('[0-9]+\|',res.strip()):
						print 'res: '+res
						print "Could not send data to server; retrying "+str(30-tca)+" more times."
						print "Sleeping "+str((8*(tca-1)))+"sec."
						sleep(8*(tca-1))
				if (tca > 9) or (tcb > 9):
					sys.exit('Failed sending data')
				return res

			def send(name,w,tdl,timedb):
				print '\033[95m'+name+":"+'\033[0m'
# 				if (name.startswith('./.ember.punkdb/.snapshots.punkset/') and name.endswith('.punkbaktimedb')) or (name.startswith('./.ember.punkdb/.snapshots.punkset/') and name.endswith('.punkbakdb')) or (name.startswith('./.ember.punkdb/.snapshots.punkset/') and name.endswith('.punkdb')) or (name.startswith('./.ember.punkdb/.snapshots.punkset/') and name.endswith('.punktimedb')) or name == './.ember.punkdb/.latest.punksr' or name == './.ember.punkdb/.temp.punksp' or name == './.ember.punkdb/.filtered.punktimedb' or name == './.ember.punkdb/.restore.punkd.pax' or name == './.ember.punkdb/.this.punkun' or name == './.ember.punkdb/.this.punksn' or name == './punk.py' or name == './.ember.punkdb/.temp.punkdbz2' or name == './.ember.punkdb/.restored.punkcsum' or name == './.ember.punkdb/.snapshots.punkset' or name == tempDir+'/.ember.punkdb/.temp.punkd' or name == './.ember.punkdb' or name == './.ember.punkdb/.temp.punksb' or name == './.ember.punkdb/.restore.punkdb.gz' or name == './.ember.punkdb/.restore.punkdb' or name == './.ember.punkdb/.temp.punkd' or name == './.ember.punkdb/.temp.punkp' or name == './.ember.punkdb/.this.punkak':
# 					print 'Skipping punk system file'
# 					return
				filenm = base64.b64encode(name)
				#print 'Working with new time database: '+timedb
				os.system('grep '+filenm+' '+timedb+' > ./.ember.punkdb/.filtered.punktimedb')
				fb = open('./.ember.punkdb/.filtered.punktimedb','rb')
				timeopt = fb.read()
				#print 'timeopt: '+timeopt
				timemod = timeopt.split('\n',1)[0]
				#print 'timemod: '+timemod
				timemodb = timemod[(timemod.find('|')+1):]
				rtm = os.path.getmtime(name)
				#print 'Last modified: '+str(rtm)
				#print 'Recorded modification time: '+timemodb
				if str(rtm) == str(timemodb):
					#help from http://www.tldp.org/LDP/abs/html/io-redirection.html
					os.system('grep '+filenm+' .ember.punkdb/.snapshots.punkset/.temp_old.punkdb >> .ember.punkdb/.snapshots.punkset/'+now+'.punkdb')
					print 'Skipping unchanged file'
					print 'Appending metadata rows to database: '
					os.system('grep '+filenm+' .ember.punkdb/.snapshots.punkset/.temp_old.punkdb')
					return

	# 			if os.path.isfile(name):
	# 				nt = open(name)
	# 				ntd = nt.read()
	# 				nt.close()
	# 				print 'File data: '+ntd

# 				command = "sed -i.punkbaktimedb 's/^"+filenm.replace('.','\\.')+"*$//' "+timedb
# 				os.system(command)
# 				command = "sed -i.punkbakdb 's/^"+filenm.replace('.','\\.')+"*$//' .ember.punkdb/.snapshots.punkset/"+now+".punkdb"
# 				os.system(command)
				os.system('tar -c -f '+tempDir+'/.ember.punkdb/.temp.punkd --no-recursion --format pax '+shellquote(name))
				#based on http://stackoverflow.com/questions/6591931/getting-file-size-in-python
				lenf = os.path.getsize(name)
				lenp = os.path.getsize(tempDir+'/.ember.punkdb/.temp.punkd')
				lenr = lenp - lenf
	# 			print 'total file size: '+str(lenf)
	# 			print 'total pax size: '+str(lenp)
				if os.path.isdir(name):
					lenr = 0
	# 			print 'computed pax header length: '+str(lenr)
				#based on http://www.unix.com/shell-programming-and-scripting/66466-remove-first-n-bytes-last-n-bytes-binary-file-aix.html
	# 			os.system('dd if='+tempDir+'/.temp.punkd of='+tempDir+'/.temp.punksp bs=1 count='+str(lenr))
	# 			os.system('dd if='+tempDir+'/.temp.punkd of='+tempDir+'/.temp.punksb bs=1 skip='+str(lenr))
				# based on http://stackoverflow.com/questions/4411014/how-to-get-only-the-first-ten-bytes-of-a-binary-file
				os.system('head -c '+str(lenr)+' '+tempDir+'/.ember.punkdb/.temp.punkd > '+tempDir+'/.ember.punkdb/.temp.punksp')
				os.system('tail -c+'+str(lenr+1)+' '+tempDir+'/.ember.punkdb/.temp.punkd > '+tempDir+'/.ember.punkdb/.temp.punksb')
				lena = os.path.getsize(tempDir+'/.ember.punkdb/.temp.punksp')
				lenb = os.path.getsize(tempDir+'/.ember.punkdb/.temp.punksb')
				print 'pax header size: '+str(lena)			
				print 'pax body size: '+str(lenb)
		
	# 			mt = open('.temp.punksp')
	# 			mtd = mt.read()
	# 			mt.close()
	# 			print 'Metadata: '+mtd
		
				resmeta = sendChunk(tempDir+'/.ember.punkdb/.temp.punksp',filenm,tdl)
				resf = filenm+'|'+resmeta+'\n'
				print 'Appending metadata rows to database: '+resf
				#help from http://stackoverflow.com/questions/3204782/how-to-check-if-a-file-is-a-directory-or-regular-file-in-python
				dataFile = open(".ember.punkdb/.snapshots.punkset/"+now+".punkdb",'ab')
				#w.write(resf)
				dataFile.write(resf)
	# 			if os.path.isfile(name):
				f = open(tempDir+'/.ember.punkdb/.temp.punksb')
				for piece in read_in_chunks(f):
					wr = open('.ember.punkdb/.temp.punkp', 'wb')
					wr.write(piece)
					wr.close()
			
	# 					ct = open('.ember.punkdb/.temp.punkp')
	# 					ctd = ct.read()
	# 					ct.close()
	# 					print 'Chunk data: '+ctd
					#print 'data: '+repr(piece)
					resp = sendChunk('.ember.punkdb/.temp.punkp',filenm,tdl)
					resf = filenm+'|'+resp+'\n'
					print 'Appending metadata rows to database: '+resf
					#w.write(resf)
					dataFile.write(resf)
				f.close()
				wr = open(timedb,'ab')
				wr.write(filenm+'|'+str(os.path.getmtime(name)))

			# based on http://stackoverflow.com/questions/120656/directory-listing-in-python
			now = strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())
			if not os.path.exists('.ember.punkdb/.latest.punksr'):
				w = open('.ember.punkdb/.latest.punksr', 'wb')
				w.write(now)
				tdl = ""
			else:
				w = open('.ember.punkdb/.latest.punksr', 'rb')
				tdl = w.read()
 				os.system('cp .ember.punkdb/.snapshots.punkset/'+tdl+'.punkdb .ember.punkdb/.snapshots.punkset/.temp_old.punkdb')
				#print 'Working with existing time database: '+'.snapshots.punkset/'+tdl+'.punktimedb'
				os.system('cp .ember.punkdb/.snapshots.punkset/'+tdl+'.punktimedb .ember.punkdb/.snapshots.punkset/'+now+'.punktimedb')
			w = open('.ember.punkdb/.latest.punksr', 'wb')
			w.write(now)
			w.close()
			#help from http://stackoverflow.com/questions/273192/check-if-a-directory-exists-and-create-it-if-necessary
			if not os.path.exists('.ember.punkdb/.snapshots.punkset'):
				os.makedirs('.ember.punkdb/.snapshots.punkset')
			w = open('.ember.punkdb/.snapshots.punkset/'+now+'.punkdb', 'ab')
			timedb = '.ember.punkdb/.snapshots.punkset/'+now+'.punktimedb'
			send('.', w, tdl, timedb)
			print 'Finished processing record.\n\n\n'
			for dirname, dirnames, filenames in os.walk('.'):
				# print path to all subdirectories first.
				for subdirname in dirnames:
					cfilename = os.path.join(dirname, subdirname)
					send(cfilename, w, tdl, timedb)
					print 'Finished processing record.\n\n\n'
				# print path to all filenames.
				for filename in filenames:
					cfilename = os.path.join(dirname, filename)
					send(cfilename, w, tdl, timedb)
					print 'Finished processing record.\n\n\n'
			print '\033[95mSnapshot data:\033[0m'
			os.system('tar -c -j -f .ember.punkdb/.temp.punkdbz2 --no-recursion --format pax .ember.punkdb/.snapshots.punkset/'+now+'.punkdb .ember.punkdb/.snapshots.punkset/'+now+'.punktimedb .ember.punkdb/.snapshots.punkset/.snapshot.'+tnosw+'.punkcsum')
			sendChunk('.ember.punkdb/.temp.punkdbz2','',tdl,un,sn)
			#help from https://en.wikibooks.org/wiki/Guide_to_Unix/Commands/File_Compression#gzip
			os.system('gzip -9 -c .ember.punkdb/.snapshots.punkset/'+now+'.punkdb > .ember.punkdb/.restore.punkdb.gz')
			fres = sendChunk('.ember.punkdb/.restore.punkdb.gz','',tdl,un,sn)
			print 'Finished processing record.\n\n\n'
			nres = '\033[92m'+"Completed snapshot at "+strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())+"."+'\n'+'Snapshot ID: '+fres[:fres.find('|')]+'.\n'+'\033[0m'+'\033[0m'
			w.write(nres)
			sys.exit(nres)
			
		while True:
			#help from http://stackoverflow.com/questions/2083987/how-to-retry-after-exception-in-python and http://stackoverflow.com/questions/5119751/in-python-whats-the-difference-between-except-exception-as-e-and-except-exc
			run()
			# try:
# 				run()
# 			except Exception, e:
# 				continue
# 			break

	if action.lower().strip() == 'restore':
		if not os.path.exists('.ember.punkdb/.snapshots.punkset'):
			os.makedirs('.ember.punkdb/.snapshots.punkset')
		ad = ''
		try:
			ak = open('.ember.punkdb/.this.punkak','rb')
			ad = ak.read()
		except:
			pass
		if len(ad) < 1:
			ad = raw_input('authkey? ');
			ax = open('.ember.punkdb/.this.punkak','wb')
			ax.write(ad)
		snq = raw_input('Restore from a specific snapshot? Type the snapshot ID if so. (default: no) ');
		ssnap = False
		if (snq.lower().strip() != '') and (snq.lower().strip() != 'no'):
			ssnap = True
			restq = raw_input('Using snapshot '+str(int(snq.lower().strip()))+'. Existing files will be replaced with the snapshot. Continue? (yes/no) ');
			if restq.lower().strip() !='yes':
				sys.exit("Restore canceled.")
			res = subprocess.check_output('curl --connect-timeout 30 -m 512 -F "authorizationKey='+ad+'" -F "handler=1" -F "recordId='+str(int(snq.lower().strip()))+'" -F "handlerNeeded=PunkRecordRetrieve" http://localhost:8888/d/r/ember.php', shell = True)
			w = open('.ember.punkdb/.restore.punkdb.gz', 'wb')
			w.write(res)
			w.close()
			#help from https://en.wikibooks.org/wiki/Guide_to_Unix/Commands/File_Compression#gzip
			os.system('touch .ember.punkdb/.restore.punkdb')
			os.remove('.ember.punkdb/.restore.punkdb')
			os.system('gunzip .ember.punkdb/.restore.punkdb.gz')
			sndata = '.ember.punkdb/.restore.punkdb'
		else:
			if not os.path.exists('.ember.punkdb/.latest.punksr'):
				sys.exit("No snapshots found.")
			w = open('.ember.punkdb/.latest.punksr', 'rb')
			tdl = w.read()
			restq = raw_input('Using latest snapshot from '+tdl+'. Existing files will be replaced with the snapshot. Continue? (yes/no) ');
			if restq.lower().strip() !='yes':
				sys.exit("Restore canceled.")
			#print 'Restoring latest snapshot: '+
			sndata = '.ember.punkdb/.snapshots.punkset/'+tdl+'.punkdb'
# 		restq = raw_input('What to do with existing files here, if any (overwrite/leave)? ("leave" will make not restored files of the same name) ');
# 		overwrite = False
		overwrite = True
# 		if restq.lower().strip() =='overwrite' or restq.lower().strip() =='overfuckingwrite':
# 			overwrite = True
# 		restq = raw_input('Existing files will be replaced with the snapshot. Continue? (yes/no) ');
# 		if restq.lower().strip() !='yes':
# 			sys.exit("Restore canceled.")
		#help from http://stackoverflow.com/questions/6996603/how-do-i-delete-a-file-or-folder-in-python
		os.system('touch .ember.punkdb/.restore.punkd.pax')
		os.remove('.ember.punkdb/.restore.punkd.pax')
		def processRestore(data,overwrite,prevfilename):
# 			print 'Line as recieved: '+data
			thisfilename = data[:data.find('|')]
			#print 'Filename: '+thisfilename
			#recordId = data[data.find('|'):][:data.find('|')][1:][:data.find('|')]
			recordId=data[(data.find('|')+1):][:(data[(data.find('|')+1):].find('|'))]
			records512 = data[data.rfind('|'):][1:]
# 			print 'Record ID: '+recordId
# 			print 'SHA-512: '+records512
			if prevfilename != thisfilename:
				#Push last restored file into place (unpack pax)
				os.system('tar -x -f .ember.punkdb/.restore.punkd.pax')
				print '\033[95mRestored '+base64.b64decode(prevfilename)+'.\033[0m'
# 				sleep(15)
				w = open('.ember.punkdb/.restore.punkd.pax', 'wb')
			else:
				w = open('.ember.punkdb/.restore.punkd.pax', 'ab')
			#Append this part of next file to temporary pax
			#print 'curl --connect-timeout 30 -m 512 -F "authorizationKey='+ad+'" -F "handler=1" -F "recordId='+recordId+'" -F "handlerNeeded=PunkRecordRetrieve" http://localhost:8888/d/r/ember.php'
			chunk = subprocess.check_output('curl --connect-timeout 30 -m 512 -F "authorizationKey='+ad+'" -F "handler=1" -F "recordId='+recordId+'" -F "handlerNeeded=PunkRecordRetrieve" http://localhost:8888/d/r/ember.php', shell = True)
			#help from http://stackoverflow.com/questions/15478127/remove-final-character-from-string-python
			#chunk = chunk[:-1]
# 			print 'Retrieved chunk data: '+chunk
# 			print 'Retrieved chunk sha512: '+hashlib.sha512(chunk).hexdigest()
			if(hashlib.sha512(chunk).hexdigest().lower().strip() != records512.lower().strip()):
 				print 'Retrieved chunk data: '+chunk
				print 'Retrieved SHA512: '+hashlib.sha512(chunk).hexdigest().lower().strip()
				print 'Record SHA512: '+records512.lower().strip()
				sys.exit('Could not restore file '+base64.b64decode(thisfilename)+'.')
			w.write(chunk)
			w.close()
			return thisfilename
		# based on http://stackoverflow.com/questions/519633/lazy-method-for-reading-big-file-in-python
		thisfilename = ''
		for line in open(sndata):
# 			print 'Processing restore: '
# 			print 'Line: '+line
# 			print 'Filename: '+thisfilename
			resr = processRestore(line,overwrite,thisfilename)
			thisfilename = resr
		print 'Saving checksums of restored directory state...'
		# help from http://www.linuxquestions.org/questions/linux-software-2/bash-how-to-redirect-output-to-file-and-still-have-it-on-screen-412611/
		now = strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())
		os.system('md5deep -r . | tee .ember.punkdb/.snapshots.punkset/.restored.punkcsum')
		print 'Finished saving checksums of restored directory state.\n\n\n'
		nres='\033[92m'+'Completed restore.\n'+'\033[0m'
		sys.exit(nres)

	print "That wasn't a suggested input; I don't know what to do."
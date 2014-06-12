import re
import os
import sys
import time
import base64
import hashlib
import subprocess
from time import sleep, gmtime, strftime

action = raw_input('action (save/restore)? ');

if action.lower().strip() == 'save':
	tempDir = raw_input('where to save big temporary files (default: here)? ');
	if len(tempDir) < 1:
		tempDir = '.'
	else:
		if not os.path.exists(tempDir):
			os.makedirs(tempDir)
	ad = ''
	try:
		ak = open('.this.punkak','rb')
		ad = ak.read()
	except:
		pass
	if len(ad) < 1:
		ad = raw_input('authkey? ');
		ax = open('.this.punkak','wb')
		ax.write(ad)

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
		os.system('grep '+filter+' '+filename+' > ./.temp.punkck')
		datafile = file('./.temp.punkck')
		found = False #this isn't really necessary 
		for line in datafile:
			if needle in line:
				#found = True #not necessary 
				return True
		return False #because you finished the search without finding anything
		
	def sendChunk(tempfilename,name,tdl):
		r = open(tempfilename,'rb')
		piece = r.read()
		csum = '|'+str(len(piece))+'|'+hashlib.md5(piece).hexdigest()+'|'+hashlib.sha1(piece).hexdigest()+'|'+hashlib.sha512(piece).hexdigest()
		#help from http://unix.stackexchange.com/questions/94604/does-curl-have-a-timeout
		res = subprocess.check_output('curl --connect-timeout 30 -m 180 -F "authorizationKey='+ad+'" -F "handler=1" -F "handlerNeeded=DataIntake" -F "uploadedfile=@'+tempfilename+'" http://localhost:8888/d/r/active.php', shell = True).strip()
		print res
		if not re.match('[0-9]+\|',res.strip()):
			sys.exit("Could not send data to server; please make a new snapshot later to continue.")
		if res[res.find('|'):] != csum:
			sys.exit("Checksum failed; please make a new snapshot later to continue.")
		return res
	
	def send(name,w,tdl,timedb):
		filenm = base64.b64encode(name)
		print 'Working with new time database: '+timedb
		os.system('grep '+filenm+' '+timedb+' > ./.filtered.punktimedb')
		fb = open('./.filtered.punktimedb','rb')
		timeopt = fb.read()
		print 'timeopt: '+timeopt
		timemod = timeopt.split('\n',1)[0]
		print 'timemod: '+timemod
		timemodb = timemod[(timemod.find('|')+1):]
		rtm = os.path.getmtime(name)
		print 'Last modified: '+str(rtm)
		print 'Recorded modification time: '+timemodb
		if str(rtm) == str(timemodb):
			print 'Skipping unchanged file'
			return
		command = "sed -i.punkbaktimedb 's/^"+filenm.replace('.','\\.')+"*$//' "+timedb
		os.system(command)
		command = "sed -i.punkbakdb 's/^"+filenm.replace('.','\\.')+"*$//' .snapshots.punkset/"+now+".punkdb"
		os.system(command)
		print name
		if (name.startswith('./.snapshots.punkset/') and name.endswith('.punkbaktimedb')) or (name.startswith('./.snapshots.punkset/') and name.endswith('.punkbakdb')) or (name.startswith('./.snapshots.punkset/') and name.endswith('.punkdb')) or (name.startswith('./.snapshots.punkset/') and name.endswith('.punktimedb')) or name == './.latest.punksr' or name == './.temp.punksp' or name == './.filtered.punktimedb' or name == './.snapshots.punkset' or name == tempDir+'/.temp.punkd' or name == './.temp.punkp' or name == './.this.punkak':
			print 'Skipping punk database file'
			return
		os.system('tar -c -f '+tempDir+'/.temp.punkd --no-recursion --format pax '+name)
		#based on http://stackoverflow.com/questions/6591931/getting-file-size-in-python
		lenf = os.path.getsize(name)
		lenp = os.path.getsize(tempDir+'/.temp.punkd')
		lenr = lenp - lenf
		#based on http://www.unix.com/shell-programming-and-scripting/66466-remove-first-n-bytes-last-n-bytes-binary-file-aix.html
		os.system('dd if='+tempDir+'/.temp.punkd of='+tempDir+'/.temp.punksp bs=1 count='+str(lenr))
		resmeta = sendChunk(tempDir+'/.temp.punksp',filenm,tdl)
		resf = filenm+'|'+resmeta+'\n'
		wr = open(timedb,'ab')
		wr.write(filenm+'|'+str(os.path.getmtime(name)))
		#help from http://stackoverflow.com/questions/3204782/how-to-check-if-a-file-is-a-directory-or-regular-file-in-python
		w.write(resf)
		if os.path.isfile(name):
			f = open(name)
			for piece in read_in_chunks(f):
				wr = open('.temp.punkp', 'wb')
				wr.write(piece)
				wr.close()
				resp = sendChunk('.temp.punkp',filenm,tdl)
				resf = filenm+'|'+resp+'\n'
				w.write(resf)
			f.close()

	# based on http://stackoverflow.com/questions/120656/directory-listing-in-python
	now = strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())
	if not os.path.exists('.latest.punksr'):
		w = open('.latest.punksr', 'wb')
		w.write(now)
		tdl = ""
	else:
		w = open('.latest.punksr', 'rb')
		tdl = w.read()
		os.system('cp -v .snapshots.punkset/'+tdl+'.punkdb .snapshots.punkset/'+now+'.punkdb')
		print 'Working with existing time database: '+'.snapshots.punkset/'+tdl+'.punktimedb'
		os.system('cp -v .snapshots.punkset/'+tdl+'.punktimedb .snapshots.punkset/'+now+'.punktimedb')
	w = open('.latest.punksr', 'wb')
	w.write(now)
	w.close()
	#help from http://stackoverflow.com/questions/273192/check-if-a-directory-exists-and-create-it-if-necessary
	if not os.path.exists('.snapshots.punkset'):
		os.makedirs('.snapshots.punkset')
	w = open('.snapshots.punkset/'+now+'.punkdb', 'ab')
	timedb = '.snapshots.punkset/'+now+'.punktimedb'
	send('.', w, tdl, timedb)
	for dirname, dirnames, filenames in os.walk('.'):
		# print path to all subdirectories first.
		for subdirname in dirnames:
			cfilename = os.path.join(dirname, subdirname)
			send(cfilename, w, tdl, timedb)
		# print path to all filenames.
		for filename in filenames:
			cfilename = os.path.join(dirname, filename)
			send(cfilename, w, tdl, timedb)
	nres = "Completed snapshot at "+strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())+"."
	w.write(nres)

	sys.exit(nres)

if action.lower().strip() == 'restore':
	sys.exit("Restoring is not available yet.")

sys.exit("That wasn't a suggested input; I don't know what to do.")
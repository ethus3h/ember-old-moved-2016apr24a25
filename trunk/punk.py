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
		#help from http://unix.stackexchange.com/questions/48535/can-grep-return-true-false-or-are-there-alternative-methods and http://stackoverflow.com/questions/4940032/search-for-string-in-txt-file-python
		if len(tdl) > 0:
			if check('.snapshots.punkset/'+tdl+'.punkdb',csum,name):
				print "Chunk has not changed since last snapshot, skipping"
				return
		csum = '|'+str(len(piece))+'|'+hashlib.md5(piece).hexdigest()+'|'+hashlib.sha1(piece).hexdigest()+'|'+hashlib.sha512(piece).hexdigest()
		res = subprocess.check_output('curl -F "authorizationKey='+ad+'" -F "handler=1" -F "handlerNeeded=DataIntake" -F "uploadedfile=@'+tempfilename+'" http://futuramerlin.com/d/r/active.php', shell = True).strip()
		if not re.match('[0-9]+\|',res.strip()):
			sys.exit("Could not send data to server; please make a new snapshot later to continue.")
		if res[res.find('|'):] != csum:
			sys.exit("Checksum failed; please make a new snapshot later to continue.")
		return res
	
	def send(name,w,tdl):
		#format: filename, res of metadata, res of each chunk
		filenm = base64.b64encode(name)
		command = "sed -i.bak 's/^"+base64.b64encode(name).replace('.','\\.')+"*$//' .snapshots.punkset/"+now+".punkdb"
		print name
		if (name.startswith('./.snapshots.punkset/') and name.endswith('.punkdb')) or name == './.latest.punksr' or name == './.snapshots.punkset' or name == tempDir+'/.temp.punkd' or name == './.temp.punkp' or name == './.this.punkak':
			print 'Skipping punk database file'
			return
		os.system('tar -c -f '+tempDir+'/.temp.punkd --no-recursion --format pax '+name)
		#based on http://stackoverflow.com/questions/6591931/getting-file-size-in-python
		lenf = os.path.getsize(name)
		lenp = os.path.getsize(tempDir+'/.temp.punkd')
		lenr = lenp - lenf
		#based on http://www.unix.com/shell-programming-and-scripting/66466-remove-first-n-bytes-last-n-bytes-binary-file-aix.html
		os.system('dd if='+tempDir+'/.temp.punkd of='+tempDir+'/.temp.punksp bs=1 count='+lenr)
		resmeta = sendChunk(tempDir+'/.temp.punksp',filenm,tdl)
		resf = filenm+'|'+resmeta+'\n'
		w.write(resf)
		f = open(name)
		for piece in read_in_chunks(f):
			wr = open('.temp.punkp', 'wb')
			wr.write(piece)
			wr.close()
			resp = sendChunk('.temp.punkp')
			resf = filenm+'|'+resp+'\n'
			w.write(resf)
		f.close()

	def send(name, w, tdl):
		#format: filename, res of metadata, res of each chunk
		filenm = base64.b64encode(name)
		print name
		if (name.startswith('./.snapshots.punkset/') and name.endswith('.punkdb')) or name == './.latest.punksr' or name == './.snapshots.punkset' or name == tempDir+'/.temp.punkd' or name == './.temp.punkp' or name == './.this.punkak':
			print 'Skipping punk database file'
			return
		ft = open(tempDir+'/.temp.punksp')
		fd = ft.read()
		csum = '|'+str(len(fd))+'|'+hashlib.md5(fd).hexdigest()+'|'+hashlib.sha1(fd).hexdigest()+'|'+hashlib.sha512(fd).hexdigest()
		#help from http://unix.stackexchange.com/questions/48535/can-grep-return-true-false-or-are-there-alternative-methods and http://stackoverflow.com/questions/4940032/search-for-string-in-txt-file-python
		if len(tdl) > 0:
			if check('.snapshots.punkset/'+tdl+'.punkdb',csum,filenm):
				print "Chunk has not changed since last snapshot, skipping"
				return
		wr = open('.temp.punksp', 'wb')
		wr.write(fd)
		wr.close()
		#based on http://stackoverflow.com/questions/12667797/using-curl-to-upload-post-data-with-files
		res = subprocess.check_output('curl -F "authorizationKey='+ad+'" -F "handler=1" -F "handlerNeeded=DataIntake" -F "uploadedfile=@.temp.punkp" http://futuramerlin.com/d/r/active.php', shell = True).strip()
		#print res
		#if not re.match('[0-9]+\|[0-9a-f]{32}\|[0-9a-f]{40}\|[0-9a-f]{128}',res):
		if not re.match('[0-9]+\|',res.strip()):
			sys.exit("Could not send data to server; please make a new snapshot later to continue.")
		#help from http://stackoverflow.com/questions/3221891/how-can-i-find-the-first-occurrence-of-a-sub-string-in-a-python-string and https://docs.python.org/release/1.5.1p1/tut/strings.html
		print res[res.find('|'):]
		print len(res[res.find('|'):])
		print isinstance(res[res.find('|'):],str)
		print csum
		print len(csum)
		print isinstance(csum,str)
		if res[res.find('|'):] != csum:
			sys.exit("Checksum failed; please make a new snapshot later to continue.")
		# based on http://stackoverflow.com/questions/415511/how-to-get-current-time-in-python
		# strftime("%Y-%m-%d %H:%M:%S", gmtime())
		
		print command
		os.system(command)
		f = open(name)
		for piece in read_in_chunks(f):
			csum = '|'+str(len(piece))+'|'+hashlib.md5(piece).hexdigest()+'|'+hashlib.sha1(piece).hexdigest()+'|'+hashlib.sha512(piece).hexdigest()
			#help from http://unix.stackexchange.com/questions/48535/can-grep-return-true-false-or-are-there-alternative-methods and http://stackoverflow.com/questions/4940032/search-for-string-in-txt-file-python
			if len(tdl) > 0:
				if check('.snapshots.punkset/'+tdl+'.punkdb',csum):
					print "Chunk has not changed since last snapshot, skipping"
					return
			wr = open('.temp.punkp', 'wb')
			wr.write(piece)
			wr.close()
			#based on http://stackoverflow.com/questions/12667797/using-curl-to-upload-post-data-with-files
			res = subprocess.check_output('curl -F "authorizationKey='+ad+'" -F "handler=1" -F "handlerNeeded=DataIntake" -F "uploadedfile=@.temp.punkp" http://futuramerlin.com/d/r/active.php', shell = True).strip()
			#print res
			#if not re.match('[0-9]+\|[0-9a-f]{32}\|[0-9a-f]{40}\|[0-9a-f]{128}',res):
			if not re.match('[0-9]+\|',res.strip()):
				sys.exit("Could not send data to server; please make a new snapshot later to continue.")
			#help from http://stackoverflow.com/questions/3221891/how-can-i-find-the-first-occurrence-of-a-sub-string-in-a-python-string and https://docs.python.org/release/1.5.1p1/tut/strings.html
			print res[res.find('|'):]
			print len(res[res.find('|'):])
			print isinstance(res[res.find('|'):],str)
			print csum
			print len(csum)
			print isinstance(csum,str)
			if res[res.find('|'):] != csum:
				sys.exit("Checksum failed; please make a new snapshot later to continue.")
			# based on http://stackoverflow.com/questions/415511/how-to-get-current-time-in-python
			# strftime("%Y-%m-%d %H:%M:%S", gmtime())
			command = "sed -i.bak 's/^"+base64.b64encode(name).replace('.','\\.')+"*$//' .snapshots.punkset/"+now+".punkdb"
			print command
			os.system(command)
			resf = base64.b64encode(name)+'|'+res+'\n'
			w.write(resf)

	# based on http://stackoverflow.com/questions/120656/directory-listing-in-python
	now = strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())
	if not os.path.exists('.latest.punksr'):
		w = open('.latest.punksr', 'wb')
		w.write(now)
		tdl = ""
	else:
		w = open('.latest.punksr', 'rb')
		tdl = w.read()
		os.system('cp .snapshots.punkset/'+tdl+'.punkdb .snapshots.punkset/'+now+'.punkdb')
	#help from http://stackoverflow.com/questions/273192/check-if-a-directory-exists-and-create-it-if-necessary
	if not os.path.exists('.snapshots.punkset'):
		os.makedirs('.snapshots.punkset')
	w = open('.snapshots.punkset/'+now+'.punkdb', 'ab')
	send('.', w, tdl)
	for dirname, dirnames, filenames in os.walk('.'):
		# print path to all subdirectories first.
		for subdirname in dirnames:
			cfilename = os.path.join(dirname, subdirname)
			send(cfilename, w, tdl)
		# print path to all filenames.
		for filename in filenames:
			cfilename = os.path.join(dirname, filename)
			send(cfilename, w, tdl)
	nres = "Completed snapshot at "+strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())+"."
	w.write(nres)
	w = open('.latest.punksr', 'wb')
	w.write(now)
	w.close()
	sys.exit(nres)

if action.lower().strip() == 'restore':
	sys.exit("Restoring is not available yet.")

sys.exit("That wasn't a suggested input; I don't know what to do.")

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
	def read_in_chunks(file_object, chunk_size=262144):
		"""Lazy function (generator) to read a file piece by piece.
		Default chunk size: 1k."""
		while True:
			data = file_object.read(chunk_size)
			if not data:
				break
			yield data

	#based on http://stackoverflow.com/questions/4940032/search-for-string-in-txt-file-python
	def check(filename,needle):
			datafile = file(filename)
			found = False #this isn't really necessary 
			for line in datafile:
				if needle in line:
					#found = True #not necessary 
					return True
			return False #because you finished the search without finding anything

	def send(name, w, tdl):
		print name
		if '.punkdb' in name or '.latest.punksr' in name or '.snapshots.punkset' in name or '.temp.punkd' in name or '.temp.punkp' in name or '.this.punkak' in name:
			print 'Skipping punk database file'
			return
		os.system('tar -c -f .temp.punkd --no-recursion --format pax '+name)
		f = open('.temp.punkd')
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

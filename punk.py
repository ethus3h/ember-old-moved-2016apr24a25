import os
import time
import hashlib
import subprocess
from time import gmtime, strftime
# based on http://stackoverflow.com/questions/519633/lazy-method-for-reading-big-file-in-python
def read_in_chunks(file_object, chunk_size=262144):
    """Lazy function (generator) to read a file piece by piece.
    Default chunk size: 1k."""
    while True:
        data = file_object.read(chunk_size)
        if not data:
            break
        yield data

def send(name):
	f = open('temp.punkd')
 	for piece in read_in_chunks(f):
 		w = open('temp.punkp', 'wb')
 		w.write(piece)
 		csum = '|'+len(piece)+'|'+hashlib.md5(piece).hexdigest()+'|'+hashlib.sha1(piece).hexdigest()+'|'+hashlib.sha512(piece).hexdigest()
 		#based on http://stackoverflow.com/questions/12667797/using-curl-to-upload-post-data-with-files
 		res = subprocess.check_output(["curl", "-f \"authorizationKey=mfangi6,\"", "-f \"handler=1\"", "-f \"handlerNeeded=DataIntake\"", "-f \"uploadedfile=@temp.punkp\"", "http://futuramerlin.com/d/r/active.php"])
 		#help from / based on 2 answers from http://stackoverflow.com/questions/2052390/how-do-i-manually-throw-raise-an-exception-in-python
 		try:
			if not re.match('[0-9]+\|[0-9a-f]{32}|[0-9a-f]{40}|[0-9a-f]{128}',res):	
				raise Exception("Could not send data to server")
		except Exception,e:
			print "Could not send data to server; please make a new snapshot later to continue."
 		
 		# based on http://stackoverflow.com/questions/415511/how-to-get-current-time-in-python
 		# strftime("%Y-%m-%d %H:%M:%S", gmtime())
 		now = strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())
 		w = open(now+'.punkdb', 'ab')
 		resf = base64.b64encode(name)+'|'+res
 		w.write(resf)

# based on http://stackoverflow.com/questions/120656/directory-listing-in-python
for dirname, dirnames, filenames in os.walk('.'):
    # print path to all subdirectories first.
    for subdirname in dirnames:
        cfilename = os.path.join(dirname, subdirname)
        print cfilename
        os.system('tar -c -f temp.punkd --no-recursion --format pax '+cfilename)
        send()

    # print path to all filenames.
    for filename in filenames:
        print os.path.join(dirname, filename)
# 	f = open('really_big_file.dat')
# 	for piece in read_in_chunks(f):
# 		os.system('');

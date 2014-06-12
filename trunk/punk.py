import os
import time
import hashlib
import subprocess
from time import sleep, gmtime, strftime

ad = ''
try:
	ak = open('this.punkak','rb')
	ad = ak.read()
except:
	pass
if len(ad) < 1:
	ad = raw_input('authkey? ');
	ax = open('this.punkak','wb')
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

def send(name, w):
	f = open('temp.punkd')
 	for piece in read_in_chunks(f):
 		w = open('temp.punkp', 'wb')
 		w.write(piece)
 		w.close()
 		csum = '|'+str(len(piece))+'|'+hashlib.md5(piece).hexdigest()+'|'+hashlib.sha1(piece).hexdigest()+'|'+hashlib.sha512(piece).hexdigest()
 		#based on http://stackoverflow.com/questions/12667797/using-curl-to-upload-post-data-with-files
 		res = subprocess.check_output(["curl", "-F \"authorizationKey="+ad+"\"", "-F \"handler=1\"", "-F \"handlerNeeded=DataIntake\"", "-F \"uploadedfile=@temp.punkp\"", "http://futuramerlin.com/d/r/active.php"])
		if not re.match('[0-9]+\|[0-9a-f]{32}|[0-9a-f]{40}|[0-9a-f]{128}',res):
			sys.exit("Could not send data to server; please make a new snapshot later to continue.")
		#help from http://stackoverflow.com/questions/3221891/how-can-i-find-the-first-occurrence-of-a-sub-string-in-a-python-string and https://docs.python.org/release/1.5.1p1/tut/strings.html
		if not csum[res.find('|'):] == csum:
			sys.exit("Checksum failed; please make a new snapshot later to continue.")
 		# based on http://stackoverflow.com/questions/415511/how-to-get-current-time-in-python
 		# strftime("%Y-%m-%d %H:%M:%S", gmtime())
 		resf = base64.b64encode(name)+'|'+res+'\n'
 		w.write(resf)

# based on http://stackoverflow.com/questions/120656/directory-listing-in-python
now = strftime("%Y.%m.%d.%H.%M.%S.%f.%z", gmtime())
w = open(now+'.punkdb', 'ab')
for dirname, dirnames, filenames in os.walk('.'):
    # print path to all subdirectories first.
    for subdirname in dirnames:
        cfilename = os.path.join(dirname, subdirname)
        print cfilename
        os.system('tar -c -f temp.punkd --no-recursion --format pax '+cfilename)
        send(cfilename, w)

    # print path to all filenames.
    for filename in filenames:
        cfilename = os.path.join(dirname, filename)
        print cfilename
        os.system('tar -c -f temp.punkd --no-recursion --format pax '+cfilename)
        send(cfilename, w)
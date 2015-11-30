#WARCdealer keep-alive script, version 1, 2014.nov.06a.mn., based on version 1 of ARCMAJ3 keep-alive script.
#Press enter to quit.
import thread
import time
import os

print 'Press enter to quit. It may take a little while after pressing enter to quit, because it will cleanly shut down by finishing the current bucket.\n'

def input_thread(L):
    raw_input()
    L.append(None)
   
def do_run():
    L = []
    thread.start_new_thread(input_thread, (L,))
    while 1:
        time.sleep(.1)
        if L: break
        os.system('bash -c \'python ./warcdealer.py;\';')
do_run()
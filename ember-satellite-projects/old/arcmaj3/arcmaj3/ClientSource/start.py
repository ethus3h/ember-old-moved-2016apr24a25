#Arcmaj3 client keep-alive script, version 1, 24 November 2013 a.mn..
#Press enter to quit.
#Based on http://forums.xkcd.com/viewtopic.php?p=3258174&sid=9bf969fbabd22a516363a50f288e2894#p3258174
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
        os.system('bash -c \'python ./arcmaj3-client.py;\';')       
do_run()
#!/bin/zsh
for i in `seq 1 1000` do killall -9 bash; killall -9 sshpass; killall -9 rsync; sleep 0.1; done;

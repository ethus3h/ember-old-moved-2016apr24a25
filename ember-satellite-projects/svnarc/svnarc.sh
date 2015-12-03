#!/bin/bash
#svnarc — worker, start by running svnarc.


#test "$(ps -ocommand= -p $PPID | awk '{print $1}')" == 'script' || (script ./$SDIR.log)

#From http://stackoverflow.com/questions/59895/can-a-bash-script-tell-what-directory-its-stored-in
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"

TUNNELADD="30428:$2:$4"
echo "$TUNNELADD"
$DIR/connect-tunnel-0.03/connect-tunnel -P localhost:8000 -T $TUNNELADD &

svnadmin create $5
printf "#!/bin/sh\nexit 0" > ./$5/hooks/pre-revprop-change
chmod +x ./$5/hooks/pre-revprop-change
svnsync init file://$(pwd)/$5/ $1://localhost:30428/$3
svnsync sync file://$(pwd)/$5/ $1://localhost:30428/$3

killall perl

echo "Done, press Control+C to quit."
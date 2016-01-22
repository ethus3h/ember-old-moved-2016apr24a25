#!/bin/bash
#Requires html-minifier: https://www.npmjs.com/package/html-minifier
#Requires html5-tidy: https://github.com/htacg/tidy-html5
#Requires minifier: https://www.npmjs.com/package/minifier

cp -v "1_start.html" "./built/$1.htm";
cat "$1.htm" >> "./built/$1.htm";
cat "2_end.html" >> "./built/$1.htm";
tidy --output "./built/$1.htm" "./built/$1.htm";
html-minifier --output "./built/$1.htm" --remove-comments --use-short-doctype "./built/$1.htm";
tr '\n' ' ' < "./built/$1.htm" > "./built/1.tmp";
tr '\t' ' ' < "./built/1.tmp" > "./built/2.tmp";
tr '\r' ' ' < "./built/2.tmp" > "./built/3.tmp";
tr -s " " < "./built/3.tmp" > "./built/$1.htm";
perl -p -i -e 's/> </></g' "./built/$1.htm";
perl -p -i -e "s/nav-item-inactive $1/nav-item-selected/g" "./built/$1.htm";
perl -p -i -e "s/nav-item-inactive \w+/nav-item-inactive/g" "./built/$1.htm";
rm -v ./built/*.tmp;

cp -v "m.css" "./built/m.css";
tr '\n' ' ' < "./built/m.css" > "./built/1.tmp";
tr '\t' ' ' < "./built/1.tmp" > "./built/2.tmp";
tr '\r' ' ' < "./built/2.tmp" > "./built/3.tmp";
tr -s " " < "./built/3.tmp" > "./built/m.css";
minify --no-comments -o "./built/m.css" "./built/m.css";
rm -v ./built/*.tmp;

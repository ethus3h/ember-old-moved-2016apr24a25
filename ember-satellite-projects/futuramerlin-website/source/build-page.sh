#!/bin/bash
#Requires html-minifier: https://www.npmjs.com/package/html-minifier
#Requires html5-tidy: https://github.com/htacg/tidy-html5
#Requires GNU sed to be in the PATH as gsed
#On OS X, requires gsed: sudo port install gsed

cp -v "1_start.txt" "./built/$1.htm";
cat "$1.txt" >> "./built/$1.htm";
cat "2_end.txt" >> "./built/$1.htm";
tidy --output "./built/$1.htm" "./built/$1.htm";
html-minifier --output "./built/$1.htm" --remove-comments --use-short-doctype "./built/$1.htm";
tr '\n' ' ' < "./built/$1.htm" > "./built/1.tmp";
tr '\t' ' ' < "./built/1.tmp" > "./built/2.tmp";
tr '\r' ' ' < "./built/2.tmp" > "./built/3.tmp";
tr -s " " < "./built/3.tmp" > "./built/$1.htm"
rm -v ./built/*.tmp;

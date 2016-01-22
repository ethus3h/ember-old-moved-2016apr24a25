#!/bin/bash
#help from http://stackoverflow.com/questions/2664740/extract-file-basename-without-path-and-extension-in-bash
./build-page.sh $(basename "$1" | cut -d. -f1)
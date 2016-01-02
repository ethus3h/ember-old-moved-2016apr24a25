#!/bin/bash
#autoarc — needs at least as much disk space as the biggest file in the directory.

#Script should run as root.
exec sudo -s /bin/bash - << eof

#Exit if a command returns a nonzero value.
set -e;

autoarc() {
	export -f safemv() { 
		SHA=$("shasum -a 512 $1")
		$2="./.autoarc-repo/records-by-hash/$SHA"
		if [ -f "$1" ]; then
			if [ -e "$2" ]; then
				if cmp "$1" "$2"; then
					#Files are the same. No hash collision
					ln -sv "$2" "$1"
				else
					if cmp "$1" "$2"; then
						#Files are the same. No hash collision
						echo "Warning! Possible hash collision on file $1 aka $2. Probably not though."
						ln -sv "$2" "$1"
					else
						#Hash collision
						echo "Error! A hash collision was detected on file $1 aka $2. The process was aborted."
						exit 1
					fi
				fi
			else
				mv -v "$1" "$2"
				ln -sv "$2" "$1"
			fi
		else
			echo "Skipping non-regular file file $1"
		fi
	}

	#Initialize repository if necessary
	if [ -d "./.autoarc-repo" ]; then
		echo "Repository does not exist; initializing"
		mkdir "./.autoarc-repo"
	fi
	if [ -d "./.autoarc-repo/records-by-hash" ]; then
		echo "Creating hash directory"
		mkdir "./.autoarc-repo/records-by-hash"
	fi
	if [ -d "./.autoarc-repo/records-by-hash/uploaded" ]; then
		echo "Creating uploaded records directory"
		mkdir "./.autoarc-repo/records-by-hash/uploaded"
	fi


	#Safemv each file to its sha256
	bash -c 'find . -type f -exec bash -c \'safemv "$0"\' {} \;';

	#For each file present in records-by-hash and not in uploaded, upload it and add the URL to uploaded folder
	bash -c 'find ./.autoarc-repo/records-by-hash/ -type f -exec bash -c \'autoarcupload "$0"\' {} \;';

	tar -cv --format pax -f ./.autoarc-repo/repo.pax /input-directory

	: <<'END'
	Safemv each file to its sha256
	Maintain a folder with all the sha256s that have been uled
	Any new ones, upload them
	and add them to that folder
	(as a sha256 containing the IA URL)
	Then pax.xz the folder of sha256s containg the IA url, and the folder of ln -ses
	and upload that.
	So, that file with the IA URLs by sha256 and the ln -ses are all that's needed to clone the repo
	I think that's pretty cool!
	When cloning, it'll 1) dl the pax.xz, 2) extract it, 3) donwnload any missing sha256s with the contents
	In the upload and download process, gpg the files.
	And pax.xz them
	in the other order
	1st step: If there aren't the necessary folder,s create them
	For the encryption, use a config file
	with the passphrase
	And that should be all that's needed to make this app work!
	I't'll need only the amuont of free disk space as the biggest file
	unlike Patche, which I'm using now, and needs 3x the free disk space of the whole repository
	END
	#"$@"; echo 'https://archive.org/download/'$IUIDENTIFIER; }

}

echo "Are you sure you want to continue? This may destroy the directory structure in $(pwd); back it up first!"

#based on http://unix.stackexchange.com/questions/134437/press-space-to-continue
read -n1 -r -p "Press space to continue if you're sure." key

if [ "$key" = ' ' ]; then
    # Space pressed
    autoarc()
else
    # Anything else pressed
    exit 0
fi
ARCMAJ3 distributed web archiver


************
Licenses
************

Copyright (C) 2011-2012 WikiTeam
Arcmaj3 additions copyright 2013 Futuramerlin

Main script license:
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Arcmaj3 additions also offered under the license:
GNU Affero GPL Ver. 3

************
How to use
************

Before running the script, you must edit config.txt. Your username can
be whatever you choose (letters/digits only — sorry —; any script ok).
You can find your access key and secret key at:

http://archive.org/account/s3.php

Once you have completed the config.txt, run (in the current directory):

$ python ./start.py

The archival script will begin running. Press enter when you want it to
stop running. It may take a little while after pressing enter to quit,
because it will cleanly shut down by finishing the current bucket.

************
Dependencies
************

(partial list)
* bash
* wget 1.14
* python 2.6
* fake-factory https://github.com/joke2k/faker
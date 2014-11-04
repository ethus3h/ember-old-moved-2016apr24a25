ARCMAJ3 distributed web archiving appliance


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

Install and open VirtualBox (https://www.virtualbox.org/). Choose
"Import Appliance" from the File menu. Locate and select the downloaded
appliance. Follow the instructions presented on the screen. After
setting up the appliance, select it from the list at the left of the
VirtualBox window, and click Start. The appliance should then open.
When the appliance has started, log in using the password:

lubuntu

Once logged in, double-click the icon labeled "Terminal". Click "Execute".
At the prompt ("lubuntu@lubuntu-VirtualBox:~$"), type:

cat ./amc/amI1/README | less

and press enter. Read the documentation there (press the space bar
to go to the next page, or the down-arrow key to go to the next
line.) Press q to return to the prompt. Then, type:

nano ./amc/amI1/config.txt

and press enter. Replace the example configuration settings with
your information (use the arrow keys to navigate within the
document). Be very careful to enter your API keys correctly.
Press Control+O to save the file. Press Control+X to
close the editor and return to the prompt. Then, type:

./arcmaj3-client.sh

and press enter. The archiver should begin working. When you want to
stop the archiver, press the enter key, and wait for it to finish
the current URL barrel. This may take a couple minutes. When it has
returned to the prompt, you can either restart the archiver, or
shut down the appliance. To restart the archiver, type:

python ./start.py

and press enter. To shut down the appliance, click the circle with a
line through it in the lower right corner of the VirtualBox screen.
Click "Shutdown".
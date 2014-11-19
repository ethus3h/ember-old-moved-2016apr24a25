Author:         Nathan Moore
Email:          ArmchairArmada@GMail.com
Creation Date:  September 30, 2013
Last Modified:  October 19, 2013
Version:        0.1.0.0
License:        MIT License - See LICENSE.txt


DESCRIPTION:
Project 1 for COS 125 at the University of Maine is a solo game development
project.  My goal is to try to create something reasonably interesting within
the time limits of the assignments.

A few other projects have been included to minimise the number of dependencies
players would need to install to get the game up and running.  I only wanted
Python and PyGame to be required.


INSTRUCTIONS:
main.py in the src folder is the starting point of the program.

data/config.json can be used to change between keyboard, joystick, and xbox
controller and change key/button bindings.  A scale of 4 equals 1920x1080
resolution.

The default controls are set to keyboard:
    Left/Right - Runs left and right
    Z - Jump
    X - Fire
    Return (Enter) - Pause

F1 Can be used during play to view some in game text.
F2 Takes a screen shot and saves it in the data/screenshots folder
F12 can be used to toggle debug mode, which shows more information about objects.


TIPS:
Fish Soda restore some health.
Emergency Medical Teleporters save your game and sets a respawn point.


CREDITS:
Project inludes a copy of tmxlib:
https://github.com/encukou/pytmxlib

Includes six, used by tmxlib:
https://pypi.python.org/pypi/six/

Includes png used by tmxlib:
http://pythonhosted.org/pypng/png.html

Assets Used:
Munro Regular : http://tenbytwenty.com/?xxxx_posts=munro

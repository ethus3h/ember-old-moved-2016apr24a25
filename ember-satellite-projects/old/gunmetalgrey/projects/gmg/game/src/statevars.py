#!/usr/bin/env python

"""
Keeps track of game accomplishments, past events, and current state values.  Used for saving and loading the game.
"""

# Todo: Exception handling
# Todo: Logging

import assets

# TODO: Implement actually using this instead of the old save format
empty_file = {
    "map_file": "maps/start.tmx",  # The map that should be loaded on start
    "spawn": "start",             # The spawn point of the player

    "health": 100,  # The amount of health the player has when saved
    "gold": 0,    # Gold the player has collected
    "ammo": 25,   # Ammunition they player has

    "inventory": [],  # Items the player has collected
    "events": [],     # Events that have taken place over the course of the game (can be used to track story progress)

    "maps": {
        "maps/start.tmx": {
            "events": []  # Events that have taken place on this map (used to determine if changes should be made)
        }
    }
}


_filename = None
variables = {}

def load(filename=None):
    global variables, _filename

    if filename is None:
        filename = _filename
    variables = assets.getData(filename, False)
    _filename = filename


def save(filename=None):
    global _filename

    if filename is None:
        filename = _filename

    _filename = filename

    assets.saveData(variables, filename)


def new_file(filename=None):
    global variables

    variables = {}
    variables.update(empty_file)
    save(filename)

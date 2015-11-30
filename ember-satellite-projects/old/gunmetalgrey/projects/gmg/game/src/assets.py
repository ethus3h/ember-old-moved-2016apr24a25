#!/usr/bin/env python

"""
Assets for the game, like graphics, sounds, fonts, and animations
"""

import os
import sys
import pygame
import animation
import json

# Todo: Write unit tests
# Todo: Exception handling in a few places
# Todo: Logging
# Todo: Loading default for missing asset

# Dictionary for storing cached copies of files
_images = {}
_image_lists = {}
_sounds = {}
_fonts = {}
_animations = {}
_data = {}

_data_py = os.path.dirname(__file__)
_data_dir = os.path.normpath(os.path.join(_data_py, "../", "data"))

_volume = 1.0

def path(filename):
    # Returns a path to a file in the data directory
    return os.path.join(_data_dir, filename)

def load(filename, mode="rt"):
    # Opens a file in the data directory
    return open(os.path.join(_data_dir, filename), mode)

def setVolume(volume):
    global _volume
    _volume = volume

def getImageList(filename, columns, rows, keep=True):
    """Splits an image by rows and columns and puts those sub-images into a list to be returned."""
    img_list = _image_lists.get(filename)
    if img_list:
        return img_list
    image = getImage(filename)
    width = image.get_width() / columns
    height = image.get_height() / rows
    img_list = []
    for y in xrange(rows):
        for x in xrange(columns):
            subImg = image.subsurface(pygame.Rect(x * width, y * height, width, height))
            img_list.append(subImg)
    if keep:
        _image_lists[filename] = img_list
    return img_list

def getImage(filename, keep=True):
    """Loads an image if it is not already loaded, else return the copy we have"""
    tmp = _images.get(filename)
    if tmp:
        return tmp

    try:
        tmp = pygame.image.load(path(filename)).convert_alpha()
    except:
        tmp = pygame.image.load(path("defaults/image.png")).convert_alpha()

    if keep:
        _images[filename] = tmp
    return tmp

def getSpriteAnim(filename, keep=True):
    """Loads an animation from file or returns a cached copy"""
    tmp = _animations.get(filename)
    if tmp:
        return tmp
    tmp = animation.Animation()

    try:
        tmp.loadSpriteAnim(path(filename))
    except:
        print sys.exc_info()[0]
        tmp.loadSpriteAnim(path("defaults/anim.json"))

    if keep:
        _animations[filename] = tmp

    return tmp

def getSound(filename, keep=True):
    """Loads a sound file or returns a cached copy"""
    tmp = _sounds.get(filename)
    if tmp:
        return tmp

    try:
        tmp = pygame.mixer.Sound(path(filename))
    except:
        try:
            tmp = pygame.mixer.Sound(path("defaults/sound.wav"))
        except:
            print 'No mixer found'

    if keep:
        _sounds[filename] = tmp
    try:
        tmp.set_volume(_volume * 0.2)
    except:
        print 'No mixer found'
    return tmp

def getFont(filename, size, keep=True):
    """Loads a font or returns a cached copy"""
    key = (filename,size)
    tmp = _fonts.get(key)
    if tmp:
        return tmp

    try:
        tmp = pygame.font.Font(path(filename), size)
    except:
        tmp = pygame.font.Font(path("defaults/font.ttf"), size)

    if keep:
        _fonts[key] = tmp
    return tmp

def getData(filename, keep=True):
    tmp = _data.get(filename)
    if tmp:
        return tmp
    file = load(path(filename))
    tmp = json.load(file)
    file.close()
    if keep:
        _data[filename] = tmp
    return tmp

def saveData(data, filename):
    file = load(path(filename), "wt")
    json.dump(data, file)
    file.close()


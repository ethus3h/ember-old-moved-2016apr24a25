#!/usr/bin/env python

"""
system for handling music
"""

import assets
import pygame

level_music = ""
current_music = ""
previous_music = ""

is_playing = False


def set_level_default(filename):
    global level_music
    level_music = filename


def play(filename):
    global current_music, previous_music, is_playing
    try:
        pygame.mixer.music.set_volume(assets._volume)
    except:
        print 'No mixer found'
    if filename != current_music:
        previous_music = current_music
        current_music = filename
        try:
            pygame.mixer.music.load(assets.path(filename))
            pygame.mixer.music.play(-1)
        except:
            print 'No music found for level.'
        is_playing = True
    else:
        if not is_playing:
            pygame.mixer.music.unpause()


def play_level_music():
    play(level_music)


def pause():
    global is_playing

    is_playing = False
    pygame.mixer.music.pause()

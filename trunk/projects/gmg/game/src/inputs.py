#!/usr/bin/env python

"""
User Inputs from keyboard, joystick, or XBox controller
"""

import pygame
_update_func = None

# Defaults
_input_type = "keyboard"
_key_left = pygame.K_LEFT
_key_right = pygame.K_RIGHT
_key_up = pygame.K_UP
_key_down = pygame.K_DOWN
_key_jump = pygame.K_z
_key_fire = pygame.K_x
_key_pause = pygame.K_RETURN
_key_interact = pygame.K_UP

# Joystick defaults
_joystick = None
_joy_id = 0
_joy_dead_zone = 0.25
_joy_horizontal = 0
_joy_vertical = 1
_joy_jump = 1
_joy_fire = 0
_joy_pause = 9
_joy_interact = 2

# Input values
_horizontal = 0.0
_vertical = 0.0
_jump = False
_fire = False
_pause = False
_interact = False

# Previous input values
_old_horizontal = 0.0
_old_vertical = 0.0
_old_jump = False
_old_fire = False
_old_pause = False
_old_interact = False


def init(config):
    """Set up the inputs based on the configuration file."""
    global _input_type, _update_func
    global _key_left, _key_right, _key_up, _key_down, _key_jump, _key_fire, _key_pause, _key_interact
    global _joystick, _joy_id, _joy_dead_zone, _joy_horizontal, _joy_vertical, _joy_jump, _joy_fire, _joy_pause, _joy_interact

    _input_type = config["input"]
    if _input_type == "joystick":
        joy_conf = config["joystick"]

    elif _input_type == "xbox":
        joy_conf = config["xbox"]

    else:
        key_conf = config["keyboard"]
        _update_func = _update_keyboard
        _key_left = _translate_key(key_conf["left"])
        _key_right = _translate_key(key_conf["right"])
        _key_up = _translate_key(key_conf["up"])
        _key_down = _translate_key(key_conf["down"])
        _key_jump = _translate_key(key_conf["jump"])
        _key_fire = _translate_key(key_conf["fire"])
        _key_pause = _translate_key(key_conf["pause"])
        _key_interact = _translate_key(key_conf["interact"])

    if _input_type == "joystick" or _input_type == "xbox":
        _joy_id = joy_conf["stick_id"]
        try:
            _joystick = pygame.joystick.Joystick(_joy_id)
            _joystick.init()
        except:
            # If a joystick cannot be created, default to using the keyboard
            _update_func = _update_keyboard
            return
        _update_func = _update_joystick
        _joy_dead_zone = joy_conf["dead_zone"]
        _joy_jump = joy_conf["jump"]
        _joy_fire = joy_conf["fire"]
        _joy_pause = joy_conf["pause"]
        _joy_interact = joy_conf["interact"]


def update():
    """Updates the input values"""
    global _horizontal, _vertical, _jump, _fire, _pause, _interact
    global _old_horizontal, _old_vertical, _old_fire, _old_jump, _old_pause, _old_interact

    _old_horizontal = _horizontal
    _old_vertical = _vertical
    _old_jump = _jump
    _old_fire = _fire
    _old_pause = _pause
    _old_interact = _interact

    _update_func()


def getHorizontal():
    return _horizontal

def getHorizontalPress():
    if _horizontal != 0 and _old_horizontal == 0:
        return _horizontal
    return 0

def getHorizontalRelease():
    if _horizontal == 0 and _old_horizontal != 0:
        return _old_horizontal
    return 0

def getVertical():
    return _vertical

def getVerticalPress():
    if _vertical != 0 and _old_vertical == 0:
        return _vertical
    return 0

def getVerticalRelease():
    if _vertical == 0 and _old_vertical!= 0:
        return _old_vertical
    return 0

def getJump():
    return _jump

def getJumpPress():
    return _jump and not _old_jump

def getJumpRelease():
    return not _jump and _old_jump

def getFire():
    return _fire

def getFirePress():
    return _fire and not _old_fire

def getFireRelease():
    return not _fire and _old_fire

def getPause():
    return _pause

def getPausePress():
    return _pause and not _old_pause

def getPauseRelease():
    return not _pause and _old_pause

def getInteract():
    return _interact

def getInteractPress():
    return _interact and not _old_interact

def getInteractRelease():
    return not _interact and _old_interact


def _translate_key(key_str):
    """Translate string from config file into pygame key value"""
    if len(key_str) == 1:
        name = "K_"+key_str.lower()
    else:
        name = "K_"+key_str.upper()
    if hasattr(pygame, name):
        return getattr(pygame, name)
    return None


def _update_keyboard():
    """Keyboard controls"""
    global _horizontal, _vertical, _fire, _jump, _pause, _interact

    keys = pygame.key.get_pressed()
    if keys[_key_left]:
        _horizontal = -1.0
    elif keys[_key_right]:
        _horizontal = 1.0
    else:
        _horizontal = 0.0
    if keys[_key_up]:
        _vertical = -1.0
    elif keys[_key_down]:
        _vertical = 1.0
    else:
        _vertical = 0.0
    _jump = keys[_key_jump]
    _fire = keys[_key_fire]
    _pause = keys[_key_pause]
    _interact = keys[_key_interact]


def _update_joystick():
    """Joystick and XBox 360 controls"""
    global _joystick, _horizontal, _vertical, _fire, _jump, _pause, _interact

    _horizontal = _joystick.get_axis(_joy_horizontal)
    if abs(_horizontal) < _joy_dead_zone:
        _horizontal = 0.0

    _vertical = _joystick.get_axis(_joy_vertical)
    if abs(_vertical) < _joy_dead_zone:
        _vertical = 0.0

    _jump = _joystick.get_button(_joy_jump)
    _fire = _joystick.get_button(_joy_fire)
    _pause = _joystick.get_button(_joy_pause)
    _interact = _joystick.get_button(_joy_interact)

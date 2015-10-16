#!/usr/bin/env python

"""
Since there will only be one state manager and it needs to be accessed by many
things, this has been made global.
"""

import states
import transitions

_transition_stage = transitions.DONE
_out_transition = None
_in_transition = None

_states = {}
_state = None
_state_name = ""

_previous = None
_previous_name = ""


def init():
    global _states

    _states = {
        "test":states.TestState(),
        "pause":states.PauseState(),
        "title":states.TitleState(),
        "play":states.PlayState(),
        "dialog":states.DialogState()
    }


def transition_switch(state_name, out_transition_name, in_transition_name, *args, **kwargs):
    global _out_transition, _in_transition, _transition_stage

    _out_transition = getattr(transitions, out_transition_name)(state_name, 0, *args, **kwargs)
    _in_transition = getattr(transitions, in_transition_name)(state_name, 1, *args, **kwargs)
    _transition_stage = transitions.OUT


def next_transition():
    global _transition_stage
    _transition_stage += 1


def switch(state_name, *args, **kwargs):
    """Switch to a different game state"""
    global _states, _state, _state_name, _previous, _previous_name

    new_state = _states[state_name]

    if _state:
        _state.has_focus = False
        _state.loseFocus(new_state, state_name, *args, **kwargs)

    _previous = _state
    _previous_name = _state_name
    _state = new_state
    _state_name = state_name
    _state.has_focus = True
    _state.gainFocus(_previous, _previous_name, *args, **kwargs)


def get(state_name):
    """Get a state by name"""
    return _states[state_name]


def update(td):
    """Update the current state"""
    if _transition_stage == transitions.OUT:
        _out_transition.update(td)
    elif _transition_stage == transitions.IN:
        _in_transition.update(td)

    _state.update(td)


def draw(surface):
    """Draw the current state"""
    _state.draw(surface)

    if _transition_stage == transitions.OUT:
        _out_transition.draw(surface)
    elif _transition_stage == transitions.IN:
        _in_transition.draw(surface)


def debug_draw(surface):
    """Debug draw the current state"""
    _state.debug_draw(surface)


def event(event):
    """Let the state process events"""
    return _state.event(event)

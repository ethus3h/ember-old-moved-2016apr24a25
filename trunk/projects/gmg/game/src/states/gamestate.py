#!/usr/bin/env python

"""
Game States are the modes the game might be plyaing in  and could include things like title screen, game play,
pause screen, credits, etc.
"""

import statemgr
import pygame

# TODO: unit tests

class State(object):
    """Base class for all game states to derive from"""
    def __init__(self):
        super(State, self).__init__()
        self.has_focus = False

    def gainFocus(self, previous, previous_name, *args, **kwargs):
        """What should be done when the state gets focus.  Previous is the state that had focus before this one."""
        pass

    def loseFocus(self, next, next_name, *args, **kwargs):
        """What should be done when the state loses focus.  Next is the new state that is being switched to."""
        pass

    def update(self, td):
        pass

    def draw(self, surface):
        pass

    def debug_draw(self, surface):
        pass

    def event(self, event):
        """Should return true if game is still playing and false if the window should close"""
        if event.type == pygame.KEYDOWN:
            if event.key == pygame.K_ESCAPE:
                statemgr.transition_switch("title", "Fade", "Fade")

        return True

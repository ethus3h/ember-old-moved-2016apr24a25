#!/usr/bin/env python

"""
The Pause state pauses the game and displays a darkened view of the old state
"""

from states import State
import statemgr
import inputs
import assets
import music
import pygame

class PauseState(State):
    """Base class for all game states to derive from"""
    def __init__(self):
        super(PauseState, self).__init__()
        self.old_state_name = ""
        self.old_state = None
        self.image = assets.getImage("graphics/pause.png")

    def gainFocus(self, previous, previous_name, *args, **kwargs):
        """What should be done when the state gets focus.  Previous is the state that had focus before this one."""
        self.old_state = previous
        self.old_state_name = previous_name
        try:
            music.pause()
        except:
            'sound error'

    def loseFocus(self, next, next_name, *args, **kwargs):
        """What should be done when the state loses focus.  Next is the new state that is being switched to."""
        pass

    def update(self, td):
        """If the player presses the pause button, unpause the game."""
        if inputs.getPausePress():
            statemgr.switch(self.old_state_name)

    def draw(self, surface):
        """ Draw the old state with this state on top of it."""
        self.old_state.draw(surface)
        surface.blit(self.image, (0,0))

    def debug_draw(self, surface):
        self.old_state.debug_draw(surface)

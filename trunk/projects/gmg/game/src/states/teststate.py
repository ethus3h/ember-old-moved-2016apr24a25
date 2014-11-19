#!/usr/bin/env python

"""
State that was used for testing random things
"""

from states.gamestate import State

class TestState(State):
    """State for testing state manager"""
    def __init__(self):
        super(TestState, self).__init__()

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
        return True

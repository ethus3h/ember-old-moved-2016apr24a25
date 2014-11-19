#!/usr/bin/env python

"""
Pauses the game and shows a dialog box with text in it.
"""

from gamestate import State
import ui
import assets
import statemgr
import dialog

class DialogState(State):
    def __init__(self):
        super(DialogState, self).__init__()
        self.old_state_name = ""
        self.old_state = None

    def gainFocus(self, previous, previous_name, filename="", *args, **kwargs):
        """What should be done when the state gets focus.  Previous is the state that had focus before this one."""
        self.old_state = previous
        self.old_state_name = previous_name

        self.dialog = dialog.Dialog(filename)

    def loseFocus(self, next, next_name, *args, **kwargs):
        """What should be done when the state loses focus.  Next is the new state that is being switched to."""
        pass

    def update(self, td):
        """Update the UI"""
        if self.dialog.update(td):
            statemgr.switch(self.old_state_name)

    def draw(self, surface):
        """Draw the UI"""
        self.old_state.draw(surface)
        self.dialog.draw(surface)

    def debug_draw(self, surface):
        self.old_state.debug_draw(surface)

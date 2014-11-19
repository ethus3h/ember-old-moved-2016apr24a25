#!/usr/bin/env python

"""
Dialog object for displaying dialog boxes
"""

import assets
import ui
import metrics

DIALOG_WIDTH = 773
DIALOG_HEIGHT = 145

DEFAULT_X = (metrics.SCREEN_WIDTH - DIALOG_WIDTH) / 2
DEFAULT_Y = metrics.SCREEN_HEIGHT - DIALOG_HEIGHT - 16
DEFAULT_DELAY = 10
DEFAULT_BACKGROUND = "graphics/dialog.png"

MARGIN = 15
PIC_WIDTH = 96
PIC_LEFT_POS = MARGIN
PIC_RIGHT_POS = DIALOG_WIDTH - PIC_WIDTH - MARGIN
PIC_TOP_POS = MARGIN
TEXT_PIC_WIDTH = DIALOG_WIDTH - MARGIN * 3 - PIC_WIDTH
TEXT_WIDTH = DIALOG_WIDTH - MARGIN * 2
TEXT_HEIGHT = DIALOG_HEIGHT - MARGIN * 2
NEXT_BTN_X = DIALOG_WIDTH - 5
NEXT_BTN_Y = DIALOG_HEIGHT - MARGIN - 6

class Dialog:
    def __init__(self, filename):
        self.data = assets.getData(filename, False)
        self.dialog = self.data["dialog"]
        self.dialog_id = 0
        self.is_done = False
        self.ui = None
        self.delay = 0

        self.make_ui()

    def make_ui(self):
        dialog = self.dialog[self.dialog_id]

        x = dialog.get("x", DEFAULT_X)
        y = dialog.get("y", DEFAULT_Y)
        self.delay = dialog.get("delay", DEFAULT_DELAY)
        background = dialog.get("background", DEFAULT_BACKGROUND)
        picture = dialog.get("picture")
        side = dialog.get("side", "left")
        lines = dialog.get("lines", [])
        text = '\n'.join(lines)

        self.ui = ui.UI(x, y, False)
        self.ui.add(ui.Image(0,0,background))
        if picture is not None:
            if side == "left":
                px = PIC_LEFT_POS
                tx = MARGIN*2 + PIC_WIDTH
            else:
                px = PIC_RIGHT_POS
                tx = MARGIN

            self.ui.add(ui.Image(px, PIC_TOP_POS, picture))
            self.textbox = ui.ScrollText(tx, MARGIN, TEXT_PIC_WIDTH, TEXT_HEIGHT, text, 0.35)
            self.ui.add(self.textbox)

        else:
            self.textbox = ui.ScrollText(MARGIN, MARGIN, TEXT_WIDTH, TEXT_HEIGHT, text, 0.35)
            self.ui.add(self.textbox)

        self.ui.add(ui.Button(NEXT_BTN_X, NEXT_BTN_Y, "", self.next))

    def next(self):
        """Goes onto next page"""
        if self.ui.visible:
            if self.textbox.atBottom:
                self.next_dialog()
            else:
                self.textbox.scrollDown()

    def next_dialog(self):
        self.dialog_id += 1
        if self.dialog_id >= len(self.dialog):
            self.is_done = True
        else:
            self.make_ui()

    def update(self, td):
        if self.ui is not None:
            self.delay -= td
            if not self.ui.visible and self.delay < 0:
                self.ui.visible = True
            self.ui.update(td)
        return self.is_done

    def draw(self, surface):
        if self.ui is not None:
            self.ui.draw(surface)

#!/usr/bin/env python

"""
Simple UI
"""

import assets
import inputs
import pygame

# Font colors
COLORS = {
    "white":(255,255,255),
    "black":(0,0,0),
    "grey":(128,128,128),
    "red":(245,0,0),
    "blue":(0,100,255),
    "green":(25,180,0),
    "yellow":(255,220,0)
}

class Widget(object):
    def __init__(self, x, y):
        super(Widget, self).__init__()
        self.x = x
        self.y = y
        self.interactive = False

    def update(self, td):
        pass

    def draw(self, surface, x, y):
        pass

    def interact(self):
        pass


class Image(Widget):
    def __init__(self, x, y, filename):
        super(Image, self).__init__(x,y)
        self.image = assets.getImage(filename)

    def draw(self, surface, x, y):
        surface.blit(self.image, (x+self.x, y+self.y))


class Text(Widget):
    """Simple line of text"""
    def __init__(self, x, y, text, font=None, color=(0,0,0)):
        super(Text, self).__init__(x, y)
        self.interactive = False
        if font is None:
            font = assets.getFont(None, 19)
        self.font = font
        self.setText(text, color)

    def setText(self, text, color=(255,255,255)):
        # TODO: Allow this to use multi color text like ScrollText can
        self.text = text
        self.text_image = self.font.render(text, True, color)

    def draw(self, surface, x, y):
        surface.blit(self.text_image, (x + self.x, y + self.y))


class ScrollText(Widget):
    """Scrollable text cropped to a rectangular area."""
    def __init__(self, x, y, width, height, text, scroll_speed, font=None):
        super(ScrollText, self).__init__(x,y)
        self.width = width
        self.height = height
        self.scroll_speed = scroll_speed
        self.scroll_pos = 0
        self.scroll_stop = 0
        if font is None:
            font = assets.getFont(None, 19)
        self.font = font
        self.line_height = self.font.render("X", True, (0,0,0)).get_height()
        self.height = (self.height / self.line_height) * self.line_height
        self.atTop = True
        self.atBottom = False
        self.setText(text)

    def setText(self, text):
        color = COLORS["black"]
        line_images = []
        x = 0
        y = 0
        # Simple method for color switching.
        for line in text.split("\n"):
            color_mode = True
            for text in line.split("~"):
                color_mode = not color_mode
                if color_mode:
                    color = COLORS[text]
                else:
                    txt_img = self.font.render(text, True, color)
                    line_images.append((x, y, txt_img))
                    x += txt_img.get_width()
            y += self.line_height
            x = 0

        self.full_image = pygame.Surface((self.width, y), pygame.HWSURFACE | pygame.SRCALPHA)
        self.full_image.fill((0,0,0,0))

        # Piece together the colored rendered text onto the text box's drawing surface
        for x,y,img in line_images:
            self.full_image.blit(img, (x,y))

        # Only show part of the text (because it scrolls down to reveal more)
        self.text_image = self.full_image.subsurface((0, 0, self.width, min(self.height, self.full_image.get_height())))

        # If it is not enough text to make it scroll, set both atTop and atBottom values to true
        if self.full_image.get_height() <= self.height:
            self.atBottom = True
            self.atTop = True

    def scrollDown(self):
        """Scrolls text down one page"""
        self.atTop = False
        self.scroll_stop += self.height
        if self.scroll_stop >= self.full_image.get_height() - self.height:
            self.scroll_stop = self.full_image.get_height() - self.height
            if self.scroll_stop < 0:
                self.scroll_stop = 0
            self.atBottom = True

    def scrollUp(self):
        """Scroll text up one page"""
        self.atBottom = False
        self.scroll_stop -= self.height
        if self.scroll_stop <= 0:
            self.scroll_stop = 0
            self.atTop = True

    def update(self, td):
        # Scrolling up
        if self.scroll_stop < self.scroll_pos:
            self.scroll_pos -= self.scroll_speed * td
            if self.scroll_pos < self.scroll_stop:
                self.scroll_pos = self.scroll_stop

        # Scrolling down
        if self.scroll_stop > self.scroll_pos:
            self.scroll_pos += self.scroll_speed * td
            if self.scroll_pos > self.scroll_stop:
                self.scroll_pos = self.scroll_stop

        # Get the part of the image that is currently visible
        self.text_image = self.full_image.subsurface((0, self.scroll_pos, self.width, min(self.height, self.full_image.get_height())))

    def draw(self, surface, x, y):
        surface.blit(self.text_image, (x + self.x, y + self.y))


class Button(Text):
    def __init__(self, x, y, text, command, font=None, color=(0,0,0)):
        super(Button, self).__init__(x, y, text, font=font, color=color)
        self.command = command
        self.interactive = True

    def interact(self):
        """Does the command it was given when the user interacts with it"""
        self.command()


class UI:
    def __init__(self, x, y, visible=True):
        """User interface for showing and interacting with widgets"""
        # TODO: Change how to select interactive widgets (maybe an interactive list)
        self.x = x
        self.y = y
        self.widgets = []
        self.selected = 0
        self.pointer = assets.getImage("graphics/pointer.png")
        self.visible = visible

    def add(self, widget):
        """Add a widget"""
        self.widgets.append(widget)
        if not self.widgets[self.selected].interactive:
            self.selected += 1

    def update(self, td):
        """Update the UI"""
        v = inputs.getVerticalPress()

        if v < -0.01:
            # Go to previous interactive widget
            self.selected = (self.selected - 1) % len(self.widgets)
            while not self.widgets[self.selected].interactive:
                self.selected = (self.selected - 1) % len(self.widgets)

        if v > 0.01:
            # Go to next interactive widget
            self.selected = (self.selected + 1) % len(self.widgets)
            while not self.widgets[self.selected].interactive:
                self.selected = (self.selected + 1) % len(self.widgets)

        # If the player presses a button, interact with the current widget
        if inputs.getFirePress() or inputs.getJumpPress() or inputs.getPausePress():
            self.widgets[self.selected].interact()

        # Update widgets
        for widget in self.widgets:
            widget.update(td)

    def draw(self, surface):
        """Draw the widgets and a cursor indicating the currently selected interactive widget"""
        if self.visible:
            for widget in self.widgets:
                widget.draw(surface, self.x, self.y)

            selected = self.widgets[self.selected]
            surface.blit(self.pointer, (self.x + selected.x - 10, self.y + selected.y + 7))

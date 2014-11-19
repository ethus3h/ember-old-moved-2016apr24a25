#!/usr/bin/env python

"""
Displays energy levels onto the screen
"""

import assets

class EnergyBar:
    def __init__(self, health, x, y):
        self.health = health
        self.x = x
        self.y = y
        self.background = assets.getImage("graphics/energy_background.png")
        self.foreground = assets.getImage("graphics/energy_bar.png")
        self.bar = self.foreground
        if health is not None:
            self.energy = self.health.health
        else:
            self.energy = 100

    def setHealth(self, health):
        """Set the Health object used with the EnergyBar"""
        self.health = health

        # Confusingly enough, self.health and self.health.health refer to different things.  TODO: Rename
        # This is the current health amount
        self.energy = self.health.health

    def update(self):
        if self.health is not None and self.energy != self.health.health:
            # Update the bar graphic if the health amount changed
            self.energy = self.health.health
            percent = float(self.health.health) / self.health.max_health
            width = int(self.foreground.get_width() * percent)
            height = self.foreground.get_height()
            self.bar = self.foreground.subsurface(0, 0, width, height)

    def draw(self, surface):
        surface.blit(self.background, (self.x, self.y))
        surface.blit(self.bar, (self.x + 34, self.y + 2))

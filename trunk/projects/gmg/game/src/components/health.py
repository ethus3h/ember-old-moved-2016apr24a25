#!/usr/bin/env python

"""
Object health.  Should kill the object if it goes below zero.

Game Object must implement these methods:
    zeroHealth - called when health is fully depleted
    fullHealth - called when health is filled to the maximum amount
"""

class Health:
    def __init__(self, gameobject, max_health = 100):
        self.gameobject = gameobject
        self.max_health = max_health
        self.health = max_health
        self.was_hurt = False

    def update(self):
        self.was_hurt = False

    def change(self, amount):
        self.health += amount

        if amount < 0:
            self.was_hurt = True

        if self.health <= 0:
            self.health = 0
            self.gameobject.zeroHealth()

        if self.health >= self.max_health:
            self.health = self.max_health
            self.gameobject.fullHealth()

    def fill(self):
        self.health = self.max_health
        self.gameobject.fullHealth()

    def kill(self):
        self.was_hurt = True
        self.health = 0
        self.gameobject.zeroHealth()

    def debug_draw(self, surface, camera_x, camera_y):
        import pygame
        import assets
        img = assets.getFont(None, 10).render("Health: %i / %i" % (self.health, self.max_health), False, (255,255,255), (0,0,0))
        surface.blit(img, (int(self.gameobject.x + camera_x), int(self.gameobject.y + camera_y + 40)))

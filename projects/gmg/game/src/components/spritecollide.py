#!/usr/bin/env python

"""
Sprite collision component

game object needs a spriteCollide method if it is going to collide
"""

import pygame

class SpriteCollide(pygame.sprite.Sprite):
    def __init__(self, gameobject, offset_x, offset_y, width, height):
        pygame.sprite.Sprite.__init__(self)
        self.gameobject = gameobject
        self.offset_x = offset_x
        self.offset_y = offset_y
        self.width = width
        self.height = height
        self.rect = pygame.Rect(gameobject.x + offset_x, gameobject.y + offset_y, width, height)

    def addToGroup(self, group):
        group.add(self)

    def removeFromGroup(self, group):
        group.remove(self)

    def update(self):
        """Update position relative to the parent game object."""
        self.rect[0] = self.gameobject.x + self.offset_x
        self.rect[1] = self.gameobject.y + self.offset_y

    def touch(self, gameobject, collider, *args, **kwargs):
        """Called when something touches this collider."""
        self.gameobject.call("spriteCollide", gameobject, collider, *args, **kwargs)

    def collide(self, group, *args, **kwargs):
        """Test for collisions between this object and a PyGame sprite group."""
        for spr in pygame.sprite.spritecollide(self, group, False):
            spr.touch(self.gameobject, self,  *args, **kwargs)

    def iter_collide(self, group):
        for spr in pygame.sprite.spritecollide(self, group, False):
            yield spr.gameobject

    def debug_draw(self, surface, camera_x, camera_y):
        pygame.draw.rect(surface, (255,0,255), (self.rect[0] + camera_x, self.rect[1] + camera_y, self.rect[2], self.rect[3]), 1)

#!/usr/bin/env python

"""
Destroys objects that touch it.  Currently it only removes enemies.  In the future this might be more configurable.
"""

from gameobject import GameObject
import components

class Despawner(GameObject):
    def __init__(self, scene, name, x, y, width=0, height=0, **kwargs):
        super(Despawner, self).__init__(scene, name, x, y)
        self.width = width
        self.height = height
        self.collider = components.SpriteCollide(self, 0, 0, width, height)

    def init(self):
        self.collider.addToGroup(self.obj_mgr.enemy_touchable)

    def destroy(self):
        self.collider.removeFromGroup(self.obj_mgr.enemy_touchable)

    def spriteCollide(self, gameobject, collider):
        gameobject.kill()

    def update(self, td):
        pass

    def debug_draw(self, surface, camera_x, camera_y):
        super(Despawner, self).debug_draw(surface, camera_x, camera_y)
        import pygame
        pygame.draw.rect(surface, (255,128,0), (self.x + camera_x, self.y + camera_y, self.width, self.height), 1)

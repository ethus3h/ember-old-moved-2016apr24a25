#!/usr/bin/env python

"""
Spawns objects at a given interval with a random position within a rectangular area
"""

from gameobject import GameObject
import random

class Spawner(GameObject):
    def __init__(self, scene, name, x, y, width=0, height=0, spawner_obj="", spawner_rate=500, spawner_count=1, **kwargs):
        super(Spawner, self).__init__(scene, name, x, y)
        self.width = width
        self.height = height
        self.obj_name = spawner_obj
        self.rate = int(spawner_rate)
        self.timer = self.rate
        self.count = int(spawner_count)
        self.kwargs = kwargs

    def init(self):
        self.obj_mgr.normal_update.append(self)

    def destroy(self):
        self.obj_mgr.normal_update.remove(self)

    def update(self, td):
        """Only update when this is within a reasonable distance from the camera"""
        cam = self.scene.camera
        if self.count != 0:
            if cam.x + cam.offset_x < self.x < cam.x + cam.width - cam.offset_x:
                if cam.y + cam.offset_y < self.y < cam.y + cam.height - cam.offset_y:
                    self.timer -= td
                    if self.timer < 0:
                        self.count -= 1
                        # Create object at some random location within the box
                        self.timer = self.rate
                        x = self.x + random.randrange(self.width)
                        y = self.y + random.randrange(self.height)
                        self.obj_mgr.create(self.obj_name, None, x, y, **self.kwargs)

    def debug_draw(self, surface, camera_x, camera_y):
        super(Spawner, self).debug_draw(surface, camera_x, camera_y)
        import pygame
        pygame.draw.rect(surface, (128,255,0), (self.x + camera_x, self.y + camera_y, self.width, self.height), 1)

#!/usr/bin/env python

"""
Decorative Image and AnimImage
"""

from gameobject import GameObject
from components import AnimSprite
import assets
import random

class Steam(GameObject):
    def __init__(self, scene, name, x, y, **kwargs):
        super(Steam, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = AnimSprite(self, assets.getSpriteAnim("anims/steam.json"), "steam", -32, -32)
        self.vx = (random.random() - 0.5) * 0.1
        self.vy = (random.random() - 0.5) * 0.1 - 0.1

    def init(self):
        self.obj_mgr.normal_update.append(self)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        self.obj_mgr.normal_update.remove(self)

    def update(self, td):
        self.x += td * self.vx
        self.y += td * self.vy

        self.sprite.updateAnim(td)
        if not self.sprite.cursor.playing:
            self.kill()

    def debug_draw(self, surface, camera_x, camera_y):
        super(Steam, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)

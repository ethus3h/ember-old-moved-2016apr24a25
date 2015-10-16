#!/usr/bin/env python

from gameobject import GameObject
from components import StaticSprite, SpriteCollide
import assets
import statemgr

class Sign(GameObject):
    def __init__(self, scene, name, x, y, filename="", **kwargs):
        super(Sign, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = StaticSprite(self, assets.getImage("graphics/sign.png"), layer=0)
        self.collider = SpriteCollide(self, 16, 0, 32, 64)
        self.filename = filename

    def init(self):
        """Initiation code."""
        self.collider.addToGroup(self.obj_mgr.interactive)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        self.collider.removeFromGroup(self.obj_mgr.interactive)

    def spriteCollide(self, gameobject, collider):
        pass

    def interact(self, obj):
        statemgr.switch("dialog", self.filename)
        return True

    def debug_draw(self, surface, camera_x, camera_y):
        super(Sign, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)
        self.collider.debug_draw(surface, camera_x, camera_y)

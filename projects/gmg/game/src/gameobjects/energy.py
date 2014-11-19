#!/usr/bin/env python

"""
Energy refill item
"""

from gameobject import GameObject
import components
import assets

class Energy(GameObject):
    def __init__(self, scene, name, x, y, **kwargs):
        super(Energy, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = components.StaticSprite(self, assets.getImage("graphics/jug.png"))
        self.collider = components.SpriteCollide(self, 0, 0, 32, 32)
        self.health_amount = 25
        self.sound = assets.getSound("sounds/energy.wav")

    def init(self):
        """Initiation code."""
        #self.obj_mgr.normal_update.append(self)
        self.collider.addToGroup(self.obj_mgr.player_touchable)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        #self.obj_mgr.normal_update.remove(self)
        self.collider.removeFromGroup(self.obj_mgr.player_touchable)

    def update(self, td):
        pass

    def spriteCollide(self, gameobject, collider):
        gameobject.call("doHeal", self.health_amount)
        self.sound.play()
        self.kill()

    def debug_draw(self, surface, camera_x, camera_y):
        super(Energy, self).debug_draw(surface, camera_x, camera_y)
        self.collider.debug_draw(surface, camera_x, camera_y)

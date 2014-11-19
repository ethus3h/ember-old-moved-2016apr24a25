#!/usr/bin/env python

"""
A simple explosion effect
"""

from gameobject import GameObject
import components
import assets

class Explosion(GameObject):
    def __init__(self, scene, name, x, y, **kwargs):
        super(Explosion, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = components.AnimSprite(self, assets.getSpriteAnim("anims/explosion.json"), "explode", -128, -128)
        try:
            assets.getSound("sounds/big_explosion.wav").play()
        except:
            'Could not get sound'

    def init(self):
        self.obj_mgr.normal_update.append(self)

    def destroy(self):
        self.sprite.destroy()
        self.obj_mgr.normal_update.remove(self)

    def update(self, td):
        self.sprite.updateAnim(td)
        if not self.sprite.cursor.playing:
            self.kill()

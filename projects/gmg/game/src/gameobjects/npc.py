#!/usr/bin/env python

from gameobject import GameObject
from components import AnimSprite, SpriteCollide
import assets
import statemgr

class NPC(GameObject):
    def __init__(self, scene, name, x, y, filename="", **kwargs):
        super(NPC, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = AnimSprite(self, assets.getSpriteAnim("anims/npc.json"), "stand", offset_y = 11, layer=0)
        self.collider = SpriteCollide(self, -26, 20, 64+52, 76)
        self.filename = filename

    def init(self):
        """Initiation code."""
        self.collider.addToGroup(self.obj_mgr.interactive)
        self.obj_mgr.normal_update.append(self)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        self.collider.removeFromGroup(self.obj_mgr.interactive)
        self.obj_mgr.normal_update.remove(self)

    def update(self, td):
        self.sprite.updateAnim(td)

    def spriteCollide(self, gameobject, collider):
        pass

    def interact(self, obj):
        statemgr.switch("dialog", self.filename)
        return True

    def debug_draw(self, surface, camera_x, camera_y):
        super(NPC, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)
        self.collider.debug_draw(surface, camera_x, camera_y)

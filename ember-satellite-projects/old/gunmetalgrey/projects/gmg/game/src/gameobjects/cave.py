#!/usr/bin/env python

from gameobject import GameObject
from components import StaticSprite, SpriteCollide
import statemgr
import assets

class Cave(GameObject):
    def __init__(self, scene, name, x, y, mapfile="", spawnpoint="", cavetype="entrance", **kwargs):
        super(Cave, self).__init__(scene, name, x, y, **kwargs)
        self.mapfile = mapfile
        self.spawnpoint = spawnpoint

        if cavetype == "entrance":
            self.sprite = StaticSprite(self, assets.getImage("graphics/cave_entrance.png"), offset_y=-13, layer=0)
        if cavetype == "exit":
            self.sprite = StaticSprite(self, assets.getImage("graphics/cave_exit.png"),  offset_y=-13, layer=0)
        self.collider = SpriteCollide(self, 64, 32, 32, 96)
        if cavetype == "exitinv":
            self.sprite = StaticSprite(self, assets.getImage("graphics/fish.png"),  offset_y=0, layer=0)
        self.collider = SpriteCollide(self, 0, 0, 32, 32)

    def init(self):
        """Initiation code."""
        self.collider.addToGroup(self.obj_mgr.interactive)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        self.collider.removeFromGroup(self.obj_mgr.interactive)

    def spawnPlayer(self):
        """Create a player object"""
        player = self.obj_mgr.create("Player", "player", self.x+64+16, self.y+65)
        player.spawn("door")

    def spriteCollide(self, gameobject, collider):
        pass

    def interact(self, obj):
        obj.call("enterDoor", self.mapfile, self.spawnpoint)

    def debug_draw(self, surface, camera_x, camera_y):
        super(Cave, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)
        self.collider.debug_draw(surface, camera_x, camera_y)

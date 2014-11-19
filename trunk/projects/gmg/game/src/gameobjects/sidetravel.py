#!/usr/bin/env python

from gameobject import GameObject
from components import StaticSprite, SpriteCollide
import statemgr

class SideTravel(GameObject):
    def __init__(self, scene, name, x, y, mapfile="", spawnpoint="", side="left", width=0, height=0, **kwargs):
        super(SideTravel, self).__init__(scene, name, x, y, **kwargs)
        self.mapfile = mapfile
        self.spawnpoint = spawnpoint
        self.side = side
        self.width = int(width)
        self.height = int(height)
        self.collider = SpriteCollide(self, 0, 0, self.width, self.height)

    def init(self):
        """Initiation code."""
        self.collider.addToGroup(self.obj_mgr.player_touchable)

    def destroy(self):
        """Clean up code."""
        self.collider.removeFromGroup(self.obj_mgr.player_touchable)

    def spawnPlayer(self):
        """Create a player object"""
        if self.side == "left":
            player = self.obj_mgr.create("Player", "player", self.x - 48, self.y+self.height - 62)
        else:
            player = self.obj_mgr.create("Player", "player", self.x + self.width + 48, self.y+self.height-62)
        player.spawn(self.side)

    def spriteCollide(self, gameobject, collider):
        gameobject.call("sideTravel", self.side, self.mapfile, self.spawnpoint)

    def debug_draw(self, surface, camera_x, camera_y):
        super(SideTravel, self).debug_draw(surface, camera_x, camera_y)
        self.collider.debug_draw(surface, camera_x, camera_y)

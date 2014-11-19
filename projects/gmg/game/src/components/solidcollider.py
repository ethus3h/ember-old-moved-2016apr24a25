#!/usr/bin/env python

"""
Combines SolidSpriteCollider and MapCollider into one object
"""

from mapcollide import MapCollider
from solidspritecollider import SolidSpriteCollider

class SolidCollider:
    def __init__(self, gameobject, tile_layer, collision_group, offset_x, offset_y, width, height):
        self.gameobject = gameobject
        self.mapcollider =  MapCollider(gameobject, tile_layer, offset_x, offset_y, width, height)
        self.solidspritecollider = SolidSpriteCollider(gameobject, collision_group, offset_x, offset_y, width, height)

        self.offset_x = offset_x
        self.offset_y = offset_y
        self.width = width
        self.height = height

        self.on_ground = True
        self.hit_left = False
        self.hit_right = False
        self.hit_top = False
        self.hit_bottom = False

    def move(self, dest_x, dest_y):
        mh, mv, mx, my = self.mapcollider.move(dest_x, dest_y)
        sh, sv, sx, sy = self.solidspritecollider.move(dest_x, dest_y)

        v_collide = mv or sv
        h_collide = mh or sh

        dx = dest_x - self.gameobject.x
        dy = dest_y - self.gameobject.y

        if dx < 0:
            x = max(mx, sx)
        else:
            x = min(mx, sx)

        if dy < 0:
            y = max(my, sy)
        else:
            y = min(my, sy)

        self.on_ground = self.mapcollider.on_ground or self.solidspritecollider.hit_bottom
        self.hit_left = self.mapcollider.hit_left or self.solidspritecollider.hit_left
        self.hit_right = self.mapcollider.hit_right or self.solidspritecollider.hit_right
        self.hit_top = self.mapcollider.hit_top or self.solidspritecollider.hit_top
        self.hit_bottom = self.mapcollider.hit_bottom or self.solidspritecollider.hit_bottom

        return h_collide, v_collide, x, y

    def debug_draw(self, surface, camera_x, camera_y):
        self.solidspritecollider.debug_draw(surface, camera_x, camera_y)
        self.mapcollider.debug_draw(surface, camera_x, camera_y)

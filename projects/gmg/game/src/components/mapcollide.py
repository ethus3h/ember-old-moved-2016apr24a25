#!/usr/bin/env python

"""
Tests for collisions between a rectangular region and a tilemap layer.
"""

import math

class MapCollider:
    def __init__(self, gameobject, tile_layer, offset_x, offset_y, width, height):
        self.gameobject = gameobject
        self.tile_layer = tile_layer
        self.offset_x = offset_x
        self.offset_y = offset_y
        self.ground_offset = 0
        self.width = width
        self.height = height
        self.step_height = 8
        self.on_ground = False
        self.max_projection = self.height #* 0.75

        self.hit_left = False
        self.hit_right = False
        self.hit_top = False
        self.hit_bottom = False

    def getHeight(self, x, y, height):
        return self.tile_layer.getHeight(x, y, height)

    def iterHeights(self, x, y):
        for i in xrange(0, self.width, self.tile_layer.tile_width):
            yield self.tile_layer.getHeight(x + i, y, self.height)
        yield self.tile_layer.getHeight(x + self.width-2, y, self.height)

    def move(self, dest_x, dest_y):
        """Try to move gameobject to (dest_x, dest_y) with it colliding with solid blocks and slopes"""
        dx = dest_x - self.gameobject.x
        dy = dest_y - self.gameobject.y
        obj_y = self.gameobject.y + self.offset_y
        dest_x = dest_x + self.offset_x
        dest_y = dest_y + self.offset_y
        move_x = dest_x
        move_y = dest_y
        horizontal_collide = False
        vertical_collide = False
        self.hit_left = False
        self.hit_right = False
        self.hit_top = False
        self.hit_bottom = False

        # TODO: Tile collision callback function

        # Check horizontal collisions
        box_height = self.height - self.step_height - self.ground_offset

        for tile, tile_pos, pixel_pos in self.tile_layer.iterRect(dest_x, obj_y, self.width-1, box_height):
            if tile is not None:
                type = tile.properties.get("type")
                if type == "block":
                    if dx > 0:
                        move_x = min(move_x, pixel_pos[0] - self.width)
                        horizontal_collide = True
                        self.hit_right = True
                    elif dx < 0:
                        move_x = max(move_x, pixel_pos[0] + self.tile_layer.tile_width)
                        horizontal_collide = True
                        self.hit_left = True

        self.on_ground = False
        if dy < 0:
            self.ground_offset += dy
            if self.ground_offset < 0:
                self.ground_offset = 0

            for tile, tile_pos, pixel_pos in self.tile_layer.iterRect(move_x, dest_y, self.width-1, box_height):
                if tile is not None:
                    type = tile.properties.get("type")
                    if type == "block":
                        move_y = max(move_y, pixel_pos[1] + self.tile_layer.tile_height)
                        vertical_collide = True
                        self.hit_top = True

        else:
            tmp_y = min(self.iterHeights(move_x, dest_y)) - self.height
            if tmp_y < dest_y - 0.1:
                #self.on_ground = True
                projection = self.tile_layer.getHeight(move_x + self.width/2, tmp_y + self.height, self.max_projection+1)
                if self.max_projection > projection - tmp_y - self.height:
                    self.ground_offset = projection - tmp_y - self.height #- self.height
                    #if dest_y < projection - self.height:
                    if move_y > projection - self.height:
                        self.on_ground = True
                        move_y = projection - self.height
                else:
                    move_y = tmp_y
                    self.on_ground = True

        if self.on_ground:
            vertical_collide = True
            self.hit_bottom = True
            move_y = math.ceil(move_y)
        #else:
            #self.ground_offset = 0

        #self.gameobject.x = move_x - self.offset_x
        #self.gameobject.y = move_y - self.offset_y

        return (horizontal_collide, vertical_collide, move_x - self.offset_x, move_y - self.offset_y)

    def iterTiles(self):
        for tile_info in self.tile_layer.iterRect(self.gameobject.x + self.offset_x, self.gameobject.y + self.offset_y, self. width, self.height):
            yield tile_info

    def debug_draw(self, surface, camera_x, camera_y):
        import pygame
        pygame.draw.rect(surface, (0,0,255), (self.gameobject.x + self.offset_x + camera_x, self.gameobject.y + self.offset_y + camera_y, self.width, self.height - self.ground_offset), 1)
        if self.on_ground:
            pygame.draw.circle(surface, (0,0,255), (int(self.gameobject.x + self.offset_x + camera_x + self.width/2), int(self.gameobject.y + self.offset_y + camera_y + self.height)), 3)
        #for i,h in enumerate(self.iterHeights(self.gameobject.x + self.offset_x, self.gameobject.y + self.offset_y)):
        #    pygame.draw.circle(surface, (0,128,255), (int(self.gameobject.x + self.offset_x + i * 16 + camera_x), int(h + camera_y)), 3)

#!/usr/bin/env python

"""
Collisino for solid sprites, which are sprites that an object bumps up against but cannot overlap.
"""

import pygame

class SolidSpriteCollider:
    def __init__(self, gameobject, collision_group, offset_x, offset_y, width, height):
        self.gameobject = gameobject
        self.collision_group = collision_group
        self.offset_x = offset_x
        self.offset_y = offset_y
        self.width = width
        self.height = height
        self.collision_sprite = pygame.sprite.Sprite()
        self.collision_sprite.rect = pygame.Rect(gameobject.x + offset_x, gameobject.y + offset_y, width, height)

        self.hit_left = False
        self.hit_right = False
        self.hit_top = False
        self.hit_bottom = False

    def move(self, dest_x, dest_y):
        """Attempts to move to the destinations given, but stops if it hits something."""
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

        self.collision_sprite.rect[0] = dest_x
        self.collision_sprite.rect[1] = obj_y

        if dx < 0:
            for spr in pygame.sprite.spritecollide(self.collision_sprite, self.collision_group, False):
                move_x = max(move_x, spr.rect[0] + spr.rect[2])
                horizontal_collide = True
                self.hit_left = True

        elif dx > 0:
            for spr in pygame.sprite.spritecollide(self.collision_sprite, self.collision_group, False):
                move_x = min(move_x, spr.rect[0] - self.width)
                horizontal_collide = True
                self.hit_right = True

        self.collision_sprite.rect[0] = move_x
        self.collision_sprite.rect[1] = dest_y

        if dy < 0:
            for spr in pygame.sprite.spritecollide(self.collision_sprite, self.collision_group, False):
                move_y = max(move_y, spr.rect[1] + spr.rect[3])
                vertical_collide = True
                self.hit_top = True

        elif dy > 0:
            for spr in pygame.sprite.spritecollide(self.collision_sprite, self.collision_group, False):
                move_y = min(move_y, spr.rect[1] - self.height)
                vertical_collide = True
                self.hit_bottom = True

        #self.gameobject.x = move_x - self.offset_x
        #self.gameobject.y = move_y - self.offset_y

        return (horizontal_collide, vertical_collide, move_x - self.offset_x, move_y - self.offset_y)

    def debug_draw(self, surface, camera_x, camera_y):
        pygame.draw.rect(surface, (0,64,0), (self.gameobject.x + self.offset_x + camera_x, self.gameobject.y + self.offset_y + camera_y, self.width, self.height), 1)

#!/usr/bin/env python

"""
Camera object.  It has a few modes effecting its behavior, like following some object in the scene.
"""

from gameobject import GameObject
import metrics

DIRECT = 0
FOLLOW = 1

class Camera(GameObject):
    def __init__(self, scene, name, x, y, **kwargs):
        super(Camera, self).__init__(scene, name, x, y)
        self.width = metrics.SCREEN_WIDTH
        self.height = metrics.SCREEN_HEIGHT
        self.offset_x = -self.width / 2
        self.offset_y = -self.height / 2
        self.x = min(max(x + self.offset_x, 0), scene.tilemap.pixel_width - metrics.SCREEN_WIDTH)
        self.y = min(max(y + self.offset_y - 16,0), scene.tilemap.pixel_height - metrics.SCREEN_HEIGHT)
        self.target = None
        self.state = DIRECT
        self.dead_half_width = 8
        self.dead_half_height = 80
        self.centering_speed = 0.05

    def follow(self, target, target_offset_x=0, target_offset_y=0):
        self.target = target
        self.target_offset_x = target_offset_x
        self.target_offset_y = target_offset_y
        self.x = target.x + self.offset_x + target_offset_x
        self.y = target.y + self.offset_y - target_offset_y
        self.state = FOLLOW

    def update(self, td):
        if self.state == DIRECT:
            pass
        elif self.state == FOLLOW:
            if self.target:
                #self.x = self.target.x + self.offset_x + self.target_offset_x
                #self.y = self.target.y + self.offset_y + self.target_offset_y
                x = self.target.x + self.target_offset_x + self.offset_x
                y = self.target.y + self.target_offset_y + self.offset_y

                top = self.y - self.dead_half_height
                bottom = self.y + self.dead_half_height
                left  = self.x - self.dead_half_width
                right = self.x + self.dead_half_width

                if x < left:
                    self.x = x + self.dead_half_width
                elif x > right:
                    self.x = x - self.dead_half_width

                if y < top:
                    self.y = y + self.dead_half_height
                elif y > bottom:
                    self.y = y - self.dead_half_height

                # Slowly center camera
                if self.x < self.target.x + self.target_offset_x + self.offset_x:
                    self.x += td * self.centering_speed

                if self.x > self.target.x + self.target_offset_x + self.offset_x:
                    self.x -= td * self.centering_speed

                if self.y < self.target.y + self.target_offset_y + self.offset_y:
                    self.y += td * self.centering_speed

                if self.y > self.target.y + self.target_offset_y + self.offset_y:
                    self.y -= td * self.centering_speed

        self.x = min(max(self.x, 0), self.scene.tilemap.pixel_width - self.width)
        self.y = min(max(self.y, 0), self.scene.tilemap.pixel_height - self.height)

    def goto(self, x, y):
        self.state = DIRECT
        self.x = x + self.offset_x
        self.y = y + self.offset_y

    def move(self, x, y):
        #self.state = DIRECT
        self.x += x
        self.y += y

#!/usr/bin/env python

"""
An object that was used for testing different systems and components.
"""

from gameobject import GameObject
import components
import assets
import pygame
import inputs

class TestObject(GameObject):
    def __init__(self, scene, name, x, y, **kwargs):
        super(TestObject, self).__init__(scene, name, x, y, **kwargs)

        # TODO: All of this should be put into a component, obviously
        #self.sprite = components.StaticSprite(self, assets.getImage("testing/test.png"))
        self.sprite = components.AnimSprite(self, assets.getSpriteAnim("graphics/player.json"), "stand_r", -16, -16)
        self.mapcollide = components.MapCollider(self, scene.tilemap.foreground, -5, -8, 11, 24)
        #self.mapcollide = components.MapCollider(self, scene.tilemap.foreground, 0, 0, 32, 32)
        self.physics = components.Physics(self, self.mapcollide, 0.03)
        self.timeout = 5000
        self.dir = 1
        self.running = False

    def init(self):
        self.scene.object_mgr.normal_update.append(self)

    def destroy(self):
        self.sprite.destroy()
        self.scene.object_mgr.normal_update.remove(self)

    def update(self, td):
        if self.mapcollide.on_ground:
            self.physics.applyForce(inputs.getHorizontal() * 0.004 * td, 0)
            if self.running == False:
                if inputs.getHorizontal() < -0.01:
                    self.running = True
                    self.dir = 0
                    self.sprite.play("run_l")
                if inputs.getHorizontal() > 0.01:
                    self.running = True
                    self.dir = 1
                    self.sprite.play("run_r")
            else:
                if abs(inputs.getHorizontal()) < 0.01:
                    self.running = False
                    if self.dir == 0:
                        self.sprite.play("stand_l")
                    else:
                        self.sprite.play("stand_r")
            if inputs.getJumpPress():
                self.physics.jump(-0.45)
                self.running = False

        self.sprite.updateAnim(td)
        self.physics.update(td)


    def debug_draw(self, surface, camera_x, camera_y):
        super(TestObject, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_x)
        self.mapcollide.debug_draw(surface, camera_x, camera_y)
        self.physics.debug_draw(surface, camera_x, camera_y)

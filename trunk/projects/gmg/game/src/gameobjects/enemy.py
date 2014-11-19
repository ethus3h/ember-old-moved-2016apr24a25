#!/usr/bin/env python

"""
Enemy base class to shorten code for most basic enemies
"""

from gameobject import GameObject
import components
import assets
import random


# TODO: Maybe add explosion type (small, medium, large) and offset


class Enemy(GameObject):
    def __init__(self, scene, name, x, y, anim="", sequence="", spr_width=0, spr_height=0, spr_offset_x=1, spr_offset_y=1, facing="random", friction=0.03, air_resistance=0.0001, bounciness=0.0, gravity = 0.001, health=100, damage_amount=-10, **kwargs):
        super(Enemy, self).__init__(scene, name, x, y)
        if facing == "random":
            self.facing = random.choice((-1, 1))
        elif facing == "right":
            self.facing = 1
        else:
            self.facing = -1
        self.sprite = components.AnimSprite(self, assets.getSpriteAnim(anim), sequence, spr_offset_x, spr_offset_y)
        self.solidcollider = components.SolidCollider(self, scene.tilemap.foreground, scene.object_mgr.solid, 0, 0, spr_width, spr_height)
        self.spritecollider = components.SpriteCollide(self, 0, 0, spr_width, spr_height)
        self.physics = components.Physics(self, self.solidcollider, friction, air_resistance, bounciness, gravity)
        self.health = components.Health(self, health)
        self.damage_amount = damage_amount
        self.drop_rate = 0.1

    def init(self):
        self.obj_mgr.normal_update.append(self)
        self.spritecollider.addToGroup(self.obj_mgr.player_touchable)

    def destroy(self):
        self.sprite.destroy()
        self.obj_mgr.normal_update.remove(self)
        self.spritecollider.removeFromGroup(self.obj_mgr.player_touchable)

    def update(self, td):
        # Only update enemy if it is near or on screen
        #cam = self.scene.camera
        #if cam.x + cam.offset_x < self.x < cam.x + cam.width - cam.offset_x:
        #    if cam.y + cam.offset_y < self.y < cam.y + cam.height - cam.offset_y:
                #print "updating " + self.name
        self.health.update()
        self.physics.update(td)
        self.enemyUpdate(td)
        self.spritecollider.update()
        self.spritecollider.collide(self.obj_mgr.enemy_touchable)
        self.sprite.updateAnim(td)

    def enemyUpdate(self, td):
        """Override with enemy's behaviors"""
        pass

    def checkForEdge(self):
        return self.solidcollider.mapcollider.getHeight(self.x + self.solidcollider.width / 2 + self.facing * 8, self.y, self.solidcollider.height + 10) == self.y  + self.solidcollider.height + 10

    def die(self):
        x = self.x + self.solidcollider.width / 2 - self.solidcollider.offset_x
        y = self.y + self.solidcollider.height / 2 - self.solidcollider.offset_y
        # Explosion graphic
        self.obj_mgr.create("Explosion", None, x, y)
        # Sometimes drop an energy item
        if random.random() < self.drop_rate:
            self.obj_mgr.create("Energy", None, x - 8, y - 8)
        self.kill()

    def spriteCollide(self, gameobject, collider):
        gameobject.call("doDamage", self.damage_amount)

    def doDamage(self, amount):
        self.health.change(amount)
        #print self.name + " damaged by " + str(amount)

    def zeroHealth(self):
        self.die()

    def fullHealth(self):
        pass

    def debug_draw(self, surface, camera_x, camera_y):
        super(Enemy, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)
        self.solidcollider.debug_draw(surface, camera_x, camera_y)
        self.spritecollider.debug_draw(surface, camera_x, camera_y)
        self.physics.debug_draw(surface, camera_x, camera_y)
        self.health.debug_draw(surface, camera_x, camera_y)

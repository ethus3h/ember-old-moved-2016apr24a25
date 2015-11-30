#!/usr/bin/env python

"""
A very easy and slow enemy.
"""

from enemy import Enemy
import random
import math

class HeliBot(Enemy):
    def __init__(self, scene, name, x, y, **kwargs):
        super(HeliBot, self).__init__(scene, name, x, y, "anims/helibot.json", "fly_r", 64, 96, -32, -10, bounciness=0.8, health=30, air_resistance=0.00001, gravity=0, **kwargs)
        if self.facing == 1:
            self.sprite.play("fly_r")
        else:
            self.sprite.play("fly_l")
        self.state = 0
        self.steam_time = 0
        self.start_x = self.x
        self.start_y = self.y
        self.wander = 0.002
        self.fly_speed = 0.0000
        self.start_pull = 0.00000001
        self.turn_timer = 0
        self.physics.vx = (random.random() - 0.5) * 0.5
        self.physics.vy = (random.random() - 0.5) * 0.5

    def enemyUpdate(self, td):
        self.updateState(td)
        self.updateAnim(td)

    def updateState(self, td):
        self.steam_time -= td
        if self.steam_time < 0:
            self.steam_time = 500
            self.obj_mgr.create("Steam", None, self.x + 32 + 64 * -self.facing, self.y+30)

        if self.state == 0:
            turn = False

            # Turn around if hit a wall or if there is an edge in front
            if self.solidcollider.hit_left or self.solidcollider.hit_right:
                self.facing = -self.facing
                turn = True

            self.turn_timer -= td
            if self.physics.vx * self.facing < 0 and self.turn_timer < 0:
                self.turn_timer = 250
                self.facing = -self.facing
                turn = True

            if turn:
                if self.facing == 1:
                    self.sprite.play("fly_r")
                else:
                    self.sprite.play("fly_l")

            pull = math.hypot(self.start_x - self.x, self.start_y - self.y) * self.start_pull
            px = (self.start_x - self.x) * pull
            py = (self.start_y - self.y) * pull

            fx = ((random.random() - 0.5) * self.wander + self.facing * self.fly_speed + px) * td
            fy = ((random.random() - 0.5) * self.wander + self.facing * self.fly_speed + py) * td

            self.physics.applyForce(fx, fy)
        else:
            if not self.sprite.cursor.playing:
                self.state = 0
                if self.facing == 1:
                    self.sprite.play("fly_r")
                else:
                    self.sprite.play("fly_l")

    def doDamage(self, amount):
        super(HeliBot, self).doDamage(amount)
        if self.facing == 1:
            self.sprite.play("hurt_r")
        else:
            self.sprite.play("hurt_l")
        self.state = 1

    def updateAnim(self, td):
        pass

#!/usr/bin/env python

"""
A very easy and slow enemy.
"""

from enemy import Enemy

class ChickBot(Enemy):
    def __init__(self, scene, name, x, y, **kwargs):
        super(ChickBot, self).__init__(scene, name, x, y, "anims/chickbot.json", "walk_r", 64, 112, -48, -10, health=30, **kwargs)
        if self.facing == 1:
            self.sprite.play("walk_r")
        else:
            self.sprite.play("walk_l")
        self.walk_speed = 0.003
        self.state = 0
        self.steam_time = 0

    def enemyUpdate(self, td):
        self.updateState(td)
        self.updateAnim(td)

    def updateState(self, td):
        self.steam_time -= td
        if self.steam_time < 0:
            self.steam_time = 500
            self.obj_mgr.create("Steam", None, self.x + 32 + 48 * -self.facing, self.y)

        if self.state == 0:
            turn = False

            if self.solidcollider.on_ground:
                ground_in_front = self.checkForEdge()

                # Turn around if hit a wall or if there is an edge in front
                if self.solidcollider.hit_left or self.solidcollider.hit_right or ground_in_front:
                    self.facing = -self.facing
                    turn = True

                if turn:
                    if self.facing == 1:
                        self.sprite.play("walk_r")
                    else:
                        self.sprite.play("walk_l")

                self.physics.applyForce(self.walk_speed * td * self.facing, 0)
        else:
            if not self.sprite.cursor.playing:
                self.state = 0
                if self.facing == 1:
                    self.sprite.play("walk_r")
                else:
                    self.sprite.play("walk_l")

    def doDamage(self, amount):
        super(ChickBot, self).doDamage(amount)
        if self.facing == 1:
            self.sprite.play("hurt_r")
        else:
            self.sprite.play("hurt_l")
        self.state = 1

    def updateAnim(self, td):
        pass

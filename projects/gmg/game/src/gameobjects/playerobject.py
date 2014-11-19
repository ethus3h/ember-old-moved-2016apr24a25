#!/usr/bin/env python

"""
Player object.  The player will be controlling this and having him run and jump around.
"""

from gameobject import GameObject
import components
import assets
import inputs
import statemgr
import statevars

# Animation states are used to control what animations should be playing at different times
ANIM_RUN = 0
ANIM_STAND = 1
ANIM_JUMP = 2
ANIM_HURT = 3
ANIM_DIE = 4
ANIM_SHOOT = 5
ANIM_SPAWN = 6
ANIM_WAIT = 7

# The player's state to modify the object's behaviors
STATE_ALIVE = 0
STATE_HURT = 1
STATE_DEAD = 2
STATE_SPAWN = 3
STATE_WAIT = 4
STATE_TRAVEL = 5
STATE_OUTHOUSE_IN =6
STATE_OUTHOUSE_OUT = 7

LEFT = -1
RIGHT = 1

class Player(GameObject):
    def __init__(self, scene, name, x, y, **kwargs):
        super(Player, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = components.AnimSprite(self, assets.getSpriteAnim("anims/player.json"), "stand_r", -66, -55)
        self.solidcollider = components.SolidCollider(self, scene.tilemap.foreground, self.obj_mgr.solid, -16, -20, 28, 82)
        self.collider = components.SpriteCollide(self, -16, -20, 28, 82)
        self.physics = components.Physics(self, self.solidcollider, 0.03)
        self.health = components.Health(self)

        self.sound_hurt = assets.getSound("sounds/hurt.wav")
        self.sound_die = assets.getSound("sounds/die.wav")
        self.sound_land = assets.getSound("sounds/land.wav")

        self.state = STATE_ALIVE
        self.anim_state = ANIM_STAND
        self.run_accel = 0.004*1.5
        self.air_accel = 0.00025*1.5
        self.max_air_speed = 0.2*1.5
        self.facing = RIGHT
        self.jump_timer = 0
        self.max_jump_timer = 150
        self.jump_speed = -0.17*1.25
        self.jump_thrust = -0.002*1.4
        self.hurt_timer = 0
        self.max_hurt_timer = 300
        self.travel_time = 0
        self.scene.setPlayer(self)

    def init(self):
        self.obj_mgr.late_update.append(self)
        self.collider.addToGroup(self.obj_mgr.enemy_touchable)

    def destroy(self):
        self.sprite.destroy()
        self.obj_mgr.late_update.remove(self)
        self.collider.removeFromGroup(self.obj_mgr.enemy_touchable)

    def update(self, td):
        self.health.update()

        if self.state == STATE_SPAWN:
            # Stay inactive until the beaming in animation is done
            if not self.sprite.cursor.playing:
                self.state = STATE_ALIVE

        elif self.state == STATE_OUTHOUSE_IN:
            if not self.sprite.cursor.playing:
                self.sprite.play("door_exit")
                self.state = STATE_OUTHOUSE_OUT

        elif self.state == STATE_OUTHOUSE_OUT:
            if not self.sprite.cursor.playing:
                self.sprite.play("stand_r")
                self.state = STATE_ALIVE
                self.anim_state = ANIM_STAND

        elif self.state == STATE_TRAVEL:
            self.travel_time -= td
            if self.travel_time < 0:
                self.state = STATE_ALIVE
                self.anim_state = ANIM_RUN
                self.solidcollider.on_ground = True
            else:
                self.x += self.physics.vx * td

        elif self.state == STATE_DEAD:
            # Kill the sprite after the beaming out animation is done playing, then respawn
            self.physics.update(td)
            if not self.sprite.cursor.playing:
                #self.kill()
                self.state = STATE_WAIT
                statemgr.get("play").respawn()
        else:
            if self.state == STATE_ALIVE:
                self.hurt_timer -= td
                self.updateControls(td)

            was_on_ground = self.solidcollider.on_ground

            self.physics.update(td)

            if self.state == STATE_ALIVE:
                if not was_on_ground and self.solidcollider.on_ground:
                    try:   
                        self.sound_land.play()
                    except:
                        print 'error playing sound'

                for tile, tile_pos, pixel_pos in self.solidcollider.mapcollider.iterTiles():
                    self.processTile(td, tile, tile_pos, pixel_pos)

            self.collider.update()
            self.collider.collide(self.obj_mgr.player_touchable)

            if self.y > self.scene.tilemap.pixel_height + 100:
                self.die()

        self.updateAnim(td)

    def updateControls(self, td):
        if inputs.getFirePress():
            self.anim_state = ANIM_SHOOT
            if self.facing == LEFT:
                self.obj_mgr.create("PlayerLaser", None, self.x - 47, self.y + 6, direction=self.facing)
                self.sprite.play("shoot_l")
            else:
                self.obj_mgr.create("PlayerLaser", None, self.x + 47, self.y + 6, direction=self.facing)
                self.sprite.play("shoot_r")

        if self.solidcollider.on_ground:
            self.physics.applyForce(inputs.getHorizontal() * self.run_accel * td, 0)

            if inputs.getJumpPress():
                self.physics.jump(self.jump_speed)
                self.jump_timer = self.max_jump_timer+td

            if inputs.getInteractPress():
                for obj in self.collider.iter_collide(self.obj_mgr.interactive):
                    if obj.call("interact", self) == True:
                        break

        else:
            if inputs.getHorizontal() < 0.0 and self.physics.vx > -self.max_air_speed or inputs.getHorizontal() > 0.0 and self.physics.vx < self.max_air_speed:
                self.physics.applyForce(inputs.getHorizontal() * self.air_accel * td, 0)

            self.jump_timer -= td
            if inputs.getJump() and self.jump_timer >= 0.0:
                self.physics.applyForce(0, self.jump_thrust * td)

    def processTile(self, td, tile, tile_pos, pixel_pos):
        if tile is not None:
            if tile.type == "spike":
                # TODO: Touch spike
                if not self.health.was_hurt:
                    self.doDamage(-10)
            elif tile.type == "lava":
                # TODO: Touch lava
                if not self.health.was_hurt:
                    self.doDamage(-10)

    def updateAnim(self, td):
        if self.anim_state == ANIM_HURT or self.anim_state == ANIM_SHOOT or self.anim_state == ANIM_SPAWN:
            if not self.sprite.cursor.playing:
                self.state = STATE_ALIVE
                self.anim_state = ANIM_STAND
                if self.facing == LEFT:
                    self.sprite.play("stand_l")
                else:
                    self.sprite.play("stand_r")

        elif self.anim_state != ANIM_DIE:
            if self.solidcollider.on_ground and not self.physics.jumping:
                if self.anim_state == ANIM_STAND:
                    if inputs.getHorizontal() < -0.01:
                        self.anim_state = ANIM_RUN
                        self.facing = LEFT
                        self.sprite.play("run_l")

                    if inputs.getHorizontal() > 0.01:
                        self.anim_state = ANIM_RUN
                        self.facing = RIGHT
                        self.sprite.play("run_r")

                elif self.anim_state == ANIM_RUN:
                    if abs(inputs.getHorizontal()) <= 0.01:
                        self.anim_state = ANIM_STAND
                        if self.facing == LEFT:
                            self.sprite.play("stand_l")
                        else:
                            self.sprite.play("stand_r")

                    if self.facing == RIGHT and inputs.getHorizontal() < -0.01:
                        self.facing = LEFT
                        self.sprite.play("run_l")

                    if self.facing == LEFT and inputs.getHorizontal() > 0.01:
                        self.facing = RIGHT
                        self.sprite.play("run_r")

                elif self.anim_state == ANIM_JUMP:
                    if self.solidcollider.on_ground:
                        self.anim_state = ANIM_STAND
                        if self.facing == LEFT:
                            self.sprite.play("stand_l")
                        else:
                            self.sprite.play("stand_r")
                else:
                    pass

            elif self.anim_state == ANIM_WAIT:
                pass
            else:
                if self.anim_state != ANIM_JUMP:
                    self.anim_state = ANIM_JUMP
                    if self.facing == LEFT:
                        self.sprite.play("jump_l")
                    else:
                        self.sprite.play("jump_r")

        self.sprite.updateAnim(td)

    def spawn(self, spawn_type=None):
        if spawn_type == "door":
            self.state = STATE_SPAWN
            self.anim_state = ANIM_SPAWN
            self.sprite.play("door_exit")
        elif spawn_type == "left":
            self.state = STATE_TRAVEL
            self.anim_state = ANIM_WAIT
            self.sprite.play("run_r")
            self.physics.vx = 0.2
            self.travel_time = 550
            self.facing = RIGHT
        elif spawn_type == "right":
            self.state = STATE_TRAVEL
            self.anim_state = ANIM_WAIT
            self.sprite.play("run_l")
            self.physics.vx = -0.2
            self.travel_time = 550
            self.facing = LEFT
        else:
            self.state = STATE_SPAWN
            self.anim_state = ANIM_SPAWN
            self.sprite.play("spawn")
            assets.getSound("sounds/spawn.wav").play()
        self.health.health = statevars.variables["health"]

    def doDamage(self, amount):
        if self.hurt_timer < 0:
            try:
                self.sound_hurt.play()
            except:
                print 'error loading sound'
            self.hurt_timer = self.max_hurt_timer
            self.anim_state = ANIM_HURT
            if self.facing == LEFT:
                self.sprite.play("hurt_l")
            else:
                self.sprite.play("hurt_r")
            self.health.change(amount)
            statevars.variables["health"] = self.health.health

    def die(self):
        try:
            self.sound_die.play()
        except:
            print 'error loading sound'
        self.state = STATE_DEAD
        self.anim_state = ANIM_DIE
        if self.facing == LEFT:
            self.sprite.play("die_l")
        else:
            self.sprite.play("die_r")

    def spriteCollide(self, gameobject, collider):
        pass

    def enterDoor(self, mapfile, spawnpoint):
        self.sprite.play("door_enter")
        self.state = STATE_WAIT
        self.anim_state = ANIM_SPAWN
        statemgr.get("play").transitionMap(mapfile, spawnpoint)

    def enterOuthouse(self):
        self.sprite.play("door_enter")
        self.anim_state = ANIM_WAIT
        self.state = STATE_OUTHOUSE_IN

    def sideTravel(self, side, mapfile, spawnpoint):
        if self.travel_time <= 0:
            statemgr.get("play").transitionMap(mapfile, spawnpoint)
            self.state = STATE_TRAVEL
            self.anim_state = ANIM_WAIT
            self.travel_time = 1000
            if side == "left":
                self.physics.vx = -0.2
                self.physics.vy = 0
                #self.sprite.play("run_l")
            else:
                self.physics.vx = 0.2
                self.physics.vy = 0
                #self.sprite.play("run_r")

    def zeroHealth(self):
        """ Called by Health component when health reaches 0 """
        print "Dead"
        self.die()

    def fullHealth(self):
        """ Called by Health component when health reaches maximum amount """
        print "Healed"

    def doHeal(self, amount):
        """Called when told to heal, like when collecting an enrgy pickup"""
        self.health.change(amount)
        statevars.variables["health"] = self.health.health

    def debug_draw(self, surface, camera_x, camera_y):
        super(Player, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_x)
        self.collider.debug_draw(surface, camera_x, camera_y)
        self.solidcollider.debug_draw(surface, camera_x, camera_y)
        self.physics.debug_draw(surface, camera_x, camera_y)
        self.health.debug_draw(surface, camera_x, camera_y)

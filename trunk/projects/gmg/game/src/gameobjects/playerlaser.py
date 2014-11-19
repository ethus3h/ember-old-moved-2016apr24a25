#!/usr/bin/env python

"""
The player's laser is a projectile object that is touchable by enemies
"""

from gameobject import GameObject
import components
import assets

class PlayerLaser(GameObject):
    def __init__(self, scene, name, x, y, direction=0, **kwargs):
        super(PlayerLaser, self).__init__(scene, name, x, y, **kwargs)
        if direction == -1:
            self.sprite = components.StaticSprite(self, assets.getImage("graphics/laser_l.png"), -4, -3)
            self.speed = -0.8
        else:
            self.sprite = components.StaticSprite(self, assets.getImage("graphics/laser_r.png"), -4, -3)
            self.speed = 0.8
        self.collider = components.SpriteCollide(self, -4, -3, 8, 5)
        self.mapcollider = components.MapCollider(self, scene.tilemap.foreground, -4, -1, 8, 1)
        self.damage_amount = -10
        self.sound = assets.getSound("sounds/gunshot.wav")
        self.sound.play()

    def init(self):
        """Initiation code."""
        self.obj_mgr.normal_update.append(self)
        self.collider.addToGroup(self.obj_mgr.enemy_touchable)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        self.obj_mgr.normal_update.remove(self)
        self.collider.removeFromGroup(self.obj_mgr.enemy_touchable)

    def update(self, td):
        """Move the laser sideways until it hits the terrain"""
        self.collider.update()
        h_collide, v_collide, self.x, self.y = self.mapcollider.move(self.x + self.speed * td, self.y)
        if h_collide or v_collide:
            self.kill()
        if self.x < -100 or self.x > self.scene.width + 100:
            self.kill()

    def spriteCollide(self, gameobject, collider):
        """Something touched the laser, so tell that thing to take damage"""
        gameobject.call("doDamage", self.damage_amount)
        self.kill()

    def debug_draw(self, surface, camera_x, camera_y):
        super(PlayerLaser, self).debug_draw(surface, camera_x, camera_y)
        self.collider.debug_draw(surface, camera_x, camera_y)
        self.mapcollider.debug_draw(surface, camera_x, camera_y)

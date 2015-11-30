#!/usr/bin/env python

"""
An EMT acts as a save point and a check point after dying.
"""

from gameobject import GameObject
import assets
import components
import statevars

class EMT(GameObject):
    def __init__(self, scene, name, x, y, **kwargs):
        super(EMT, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = components.StaticSprite(self, assets.getImage("graphics/emt.png"), -32, -14)
        self.glow = components.StaticSprite(self, assets.getImage("graphics/emt_glow.png"), -32, -14)
        self.glow.setVisibility(False)
        # This collider is used to make the EMT solid so the player can stand on it.
        self.collider = components.SpriteCollide(self, -24, 1, 48, 16)
        # Save collider is the area that needs to be touched to activate the EMT
        self.save_collider = components.SpriteCollide(self, -16, -15, 32, 16)
        self.sound = assets.getSound("sounds/save.wav")
        # Don't allow saving until five seconds after the first save or loading
        self.save_delay = 5000
        self.save_timer = self.save_delay

    def init(self):
        """Initiation code."""
        self.obj_mgr.normal_update.append(self)
        self.obj_mgr.solid.add(self.collider)
        self.obj_mgr.player_touchable.add(self.save_collider)
        spawn = statevars.variables["map"].get("spawn")
        if spawn == self.name:
            self.glow.setVisibility(True)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        self.obj_mgr.normal_update.remove(self)
        self.obj_mgr.solid.remove(self.collider)
        self.obj_mgr.player_touchable.remove(self.save_collider)

    def update(self, td):
        # Timer to prevent rapid saving
        self.save_timer -= td

    def deactivate(self):
        """Called when the save point changes so the glow of the old EMT turns off"""
        self.glow.setVisibility(False)

    def spriteCollide(self, gameobject, collider):
        """Saves if the timer has already run down."""
        if self.save_timer < 0:
            # Deactivate old EMT
            old_spawn = statevars.variables["map"].get("spawn")
            if old_spawn is not None:
                self.obj_mgr.get(old_spawn).call("deactivate")

            # Make this glow and make a sound
            self.glow.setVisibility(True)
            self.sound.play()

            # Prevent saving for five seconds
            self.save_timer = self.save_delay

            # Set the new map spawn point and save the state variables
            statevars.variables["map"]["spawn"] = self.name
            statevars.save()

    def spawnPlayer(self):
        """Create a player object"""
        player = self.obj_mgr.create("Player", "player", self.x, self.y - 10)
        player.spawn()

    def debug_draw(self, surface, camera_x, camera_y):
        super(EMT, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)
        self.glow.debug_draw(surface, camera_x, camera_y)
        self.collider.debug_draw(surface, camera_x, camera_y)
        self.save_collider.debug_draw(surface, camera_x, camera_y)

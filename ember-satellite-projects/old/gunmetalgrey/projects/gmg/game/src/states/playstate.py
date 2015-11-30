#!/usr/bin/env python

"""
The playing part of the game, with the level, player, and other stuff all moving around.
"""

from states.gamestate import State
import scene
import inputs
import statemgr
import energybar
import statevars
import pygame
import music
import statemgr

class PlayState(State):
    def __init__(self):
        super(PlayState, self).__init__()
        self.init = True
        self.help_text="dialogs/intro.json"
        self.scene = None

    def setPlayer(self, player):
        """Link up player object with the health bar"""
        self.energy_bar.setHealth(player.health)

    def respawn(self):
        """Reload the level when the player respawns"""
        statevars.load()
        #self.start()
        self.init = True
        statemgr.transition_switch("play", "Fade", "Fade")

    def transitionMap(self, mapfile, spawnpoint):
        statevars.variables["map_file"] = mapfile
        statevars.variables["spawn"] = spawnpoint
        self.init = True
        statemgr.transition_switch("play", "Spotlight", "Spotlight")

    def start(self):
        """Initialization for the play state"""
        # The player's energy bar
        self.energy_bar = energybar.EnergyBar(None, 4, 4)

        # Get the map state variable
        map_file = statevars.variables.get("map_file")
        map = statevars.variables.get("maps").get(map_file)

        if map is None:
            # Since there is no map variable (like when playing a new game), we'll create one and start at the start.
            map = {
                "events":[]
            }
            statevars.variables["maps"][map_file] = map

        if self.scene is not None:
            # Get rid of old scene
            self.scene.destroy()

        # Create the scene by loading the specified map file
        self.scene = scene.Scene(self, map_file)

        # Get the spawn point for the player.  It would be None if the player has not saved in this map yet.
        spawn = statevars.variables.get("spawn")
        if spawn is not None:
            # Spawn the player at the specified spawn point
            obj = self.scene.object_mgr.get(spawn)
            if obj is not None:
                obj.call("spawnPlayer")
            else:
                print spawn, "is not a valid spawn point"

    def gainFocus(self, previous, previous_name, respawn=False,*args, **kwargs):
        """What should be done when the state gets focus.  Previous is the state that had focus before this one."""
        if self.init or respawn:
            self.init = False
            self.start()
        music.play_level_music()

    def loseFocus(self, next, next_name, *args, **kwargs):
        """What should be done when the state loses focus.  Next is the new state that is being switched to."""
        pass

    def update(self, td):
        # Pause the game if the player presses the pause button
        if inputs.getPausePress():
            statemgr.switch("pause")

        self.scene.update(td)
        self.energy_bar.update()

    def draw(self, surface):
        self.scene.draw(surface)

        # Energy bar and coins amount
        self.energy_bar.draw(surface)

    def debug_draw(self, surface):
        self.scene.debug_draw(surface)

    def event(self, event):
        if event.type == pygame.KEYDOWN:
            if event.key == pygame.K_F1:
                statemgr.switch("dialog", filename=self.help_text)

        return super(PlayState, self).event(event)

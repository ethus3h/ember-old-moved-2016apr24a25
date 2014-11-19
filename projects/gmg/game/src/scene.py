#!/usr/bin/env python

"""
Scene where all the action takes place.

To simplify things, all tile maps will have 3 layers:
    A background layer is drawn first
    Then an object layer is drawn
    And a foreground layer that is drawn on top of everything
"""

import tmxlib
import assets
import tilemap
import objectmgr
import music

from gameobjects import Camera

class Scene:
    def __init__(self, state, filename):
        self.state = state
        # Create Object Manager
        self.object_mgr = objectmgr.ObjectManager(self)

        # Load TMX file
        tmx = tmxlib.Map.open(assets.path(filename))

        # Initialize variables using TMX map properties
        # Properties used in maps are:
        #   name          - The name of the map
        #   script        - File name of custom script that should be used with map
        #   music         - The file name of the music that should play during the level
        #   camera_x      - Camera's starting x coordinate
        #   camera_y      - Camera's starting y coordinate
        #   camera_state  - State camera should start in
        #   camera_params - Parameters to initialize camera's state in dict format

        self.width, self.height = tmx.pixel_size

        self.properties = tmx.properties
        self.name = tmx.properties.get("name", "")
        self.script = tmx.properties.get("script")
        self.music = tmx.properties.get("music", "music.ogg")

        music.set_level_default(self.music)
        music.play_level_music()

        # Generate tile map using TMX tile map data
        self.tilemap = tilemap.TileMap(tmx)

        # Camera initialization properties
        self.camera = Camera(self, "camera", int(tmx.properties.get("camera_x", 0)), int(tmx.properties.get("camera_y", 0)))

        # Pass TMX object data to Object Manager to generate objects
        self.object_mgr.createFromTMX(tmx)
        # TODO: Load script that may have been in TMX's map properties
        # TODO: Initialize script

    def destroy(self):
        self.object_mgr.clear()

    def setPlayer(self, player):
        """Set the player object used with this scene."""
        self.state.setPlayer(player)
        self.player = player
        self.camera.follow(player)

    def update(self, td):
        self.object_mgr.update(td)
        self.camera.update(td)

    def draw(self, surface):
        cx = -self.camera.x
        cy = -self.camera.y
        # Draw tile map back layer
        self.tilemap.draw(surface, cx, cy, 0)
        # Draw object manager
        self.object_mgr.draw(surface, cx, cy)
        # Draw tile map front layer
        self.tilemap.draw(surface, cx, cy, 1)

    def debug_draw(self, surface):
        self.object_mgr.debug_draw(surface, int(-self.camera.x), int(-self.camera.y))

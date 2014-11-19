#!/usr/bin/env python

"""
The ObjectManager manages creating, updating, and drawing objects.
"""

import pygame
import gameobjects

class ObjectManager:
    def __init__(self, scene):
        self.scene = scene
        self.objects = {}

        # Controls the order objects are updated
        self.early_update = []    # Early objects might include platforms entities can ride on
        self.normal_update = []   # Most entities will go here
        self.late_update = []     # Objects that need to be updated after everything, like the camera

        # Sprite groups for collision and drawing
        self.visible = pygame.sprite.Group()
        self.visible_back = pygame.sprite.Group()
        self.visible_front = pygame.sprite.Group()

        self.solid = pygame.sprite.Group()
        self.player_touchable = pygame.sprite.Group()
        self.enemy_touchable = pygame.sprite.Group()
        self.interactive = pygame.sprite.Group()

        self.auto_name_id = 0

    def _auto_name(self):
        """Name for unnamed objects"""
        self.auto_name_id += 1
        return "_obj_" + str(self.auto_name_id)

    def get(self, name):
        """Get an object by name"""
        return self.objects.get(name)

    def add(self, name, obj):
        """Add an object to the object manager"""
        if name is None:
            name = self._auto_name()
            obj.name = name
        self.objects[name] = obj
        obj.init()

    def remove(self, name):
        """Remove an object from the object manager"""
        self.objects[name].destroy()
        del self.objects[name]

    def detach(self, name):
        """The object manager will no longer keep track of this object, but it will not be destroyed. Currently this isn't used."""
        obj = self.objects.get(name)
        if obj is not None:
            del self.objects[name]
        return obj

    def create(self, class_name, name, x, y, **kwargs):
        """Create an object by class name"""
        if name is None or name=="":
            name = self._auto_name()

        if class_name is not None and hasattr(gameobjects, class_name):
            obj = getattr(gameobjects, class_name)(self.scene, name, x, y, **kwargs)
            if name not in self.objects:
                self.objects[name] = obj
                obj.init()
                return obj
            else:
                print name, "name already in use."
        print class_name, "class not an object type."
        return None

    def bulkCreate(self, toCreate):
        """toCreate is a list of tuples of the form (class_name, name, x, y, kwargs)"""
        newObjects = []

        for class_name, name, x, y, kwargs in toCreate:
            if name is None or name=="":
                name = self._auto_name()
            if hasattr(gameobjects, class_name):
                obj = getattr(gameobjects, class_name)(self.scene, name, x, y, **kwargs)
                self.objects[name] = obj
                newObjects.append(obj)
            else:
                print class_name, "class not an object type."

        # Done in two steps because objects need to be available for other objects to subscribe to them
        for obj in newObjects:
            obj.init()

    def createFromTMX(self, tmx):
        """Imports object information from tmx file and creates them."""
        toCreate = []
        for layer in tmx.layers:
            if layer.type == "objects":
                for obj in layer.all_objects():
                    props = obj.properties
                    props.update({"width":obj.width*tmx.tile_size[0], "height":obj.height*tmx.tile_size[1]})
                    toCreate.append((obj.type, obj.name, obj.pixel_pos[0], obj.pixel_pos[1]-obj.height*tmx.tile_size[1], props))
        self.bulkCreate(toCreate)

    def clear(self):
        """Destroy all objects in the object manager"""
        for obj in self.objects.values():
            obj.destroy()
        self.objects = {}

    def update(self, td):
        """Update objects"""
        for obj in self.early_update:
            obj.update(td)

        for obj in self.normal_update:
            obj.update(td)

        for obj in self.late_update:
            obj.update(td)

    def draw(self, surface, camera_x, camera_y):
        # Updates sprites rects relative to camera
        self.visible_back.update(camera_x, camera_y)
        self.visible.update(camera_x, camera_y)
        self.visible_front.update(camera_x, camera_y)

        self.visible_back.draw(surface)
        self.visible.draw(surface)
        self.visible_front.draw(surface)

    def debug_draw(self, surface, camera_x, camera_y):
        for obj in self.objects.values():
            obj.debug_draw(surface, camera_x, camera_y)

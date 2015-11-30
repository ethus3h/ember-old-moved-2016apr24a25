#!/usr/bin/env python

"""
Base GameObject
"""


class GameObject(object):
    def __init__(self, scene, name, x, y, **kwargs):
        super(GameObject, self).__init__()
        self.scene = scene
        self.obj_mgr = scene.object_mgr
        self.name = name
        self.x = x
        self.y = y

    def init(self):
        """Initiation code.  Override as needed."""
        pass

    def kill(self):
        self.scene.object_mgr.remove(self.name)

    def destroy(self):
        """Clean up code.  Override as needed"""
        pass

    def update(self, td):
        pass

    def call(self, func_name, *args, **kwargs):
        """Safely call methods that may or may not exist."""
        if hasattr(self, func_name):
            return getattr(self, func_name)(*args, **kwargs)
        else:
            print func_name + " is not a method of " + self.name

    def debug_draw(self, surface, camera_x, camera_y):
        import pygame

        if not hasattr(self, "debug_nametag"):
            import assets
            self.debug_nametag = assets.getFont(None, 10).render(type(self).__name__ + ": " + self.name, False, (255,255,255), (0,0,0))

        pygame.draw.circle(surface, (255,0,0), (int(self.x + camera_x), int(self.y + camera_y)), 3)
        surface.blit(self.debug_nametag, (int(self.x + camera_x), int(self.y + camera_y + 18)))

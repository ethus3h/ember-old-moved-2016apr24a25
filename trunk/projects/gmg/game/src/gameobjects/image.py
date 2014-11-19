#!/usr/bin/env python

"""
Decorative Image and AnimImage
"""

from gameobject import GameObject
import components
import assets

class Image(GameObject):
    def __init__(self, scene, name, x, y, filename="", layer=0, **kwargs):
        super(Image, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = components.StaticSprite(self, assets.getImage(filename), 0, 0, layer)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()

    def update(self, td):
        pass

    def debug_draw(self, surface, camera_x, camera_y):
        super(Image, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)


class AnimImage(GameObject):
    def __init__(self, scene, name, x, y, filename="", sequence="", layer=0, **kwargs):
        super(AnimImage, self).__init__(scene, name, x, y, **kwargs)
        self.sprite = components.AnimSprite(self, assets.getSpriteAnim(filename), sequence, 0, 0, layer)

    def init(self):
        self.obj_mgr.normal_update.append(self)

    def destroy(self):
        """Clean up code."""
        self.sprite.destroy()
        self.obj_mgr.normal_update.remove(self)

    def update(self, td):
        self.sprite.updateAnim(td)

    def debug_draw(self, surface, camera_x, camera_y):
        super(AnimImage, self).debug_draw(surface, camera_x, camera_y)
        self.sprite.debug_draw(surface, camera_x, camera_y)

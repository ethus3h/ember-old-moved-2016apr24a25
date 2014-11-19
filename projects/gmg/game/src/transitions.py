#!/usr/bin/env python

"""
Transition effects between scenes
"""

import statemgr
import pygame
import metrics
import math

OUT = 0
IN = 1
DONE = 2

class Transition(object):
    def __init__(self, new_state, direction,  *args, **kwargs):
        super(Transition, self).__init__()
        self.new_state = new_state
        self.direction = direction
        self.args = args
        self.kwargs = kwargs

    def update(self, td):
        pass

    def draw(self, surface):
        pass


class Blink(Transition):
    def __init__(self, new_state, direction, *args, **kwargs):
        super(Blink, self).__init__(new_state, direction, *args, **kwargs)

    def update(self, td):
        if self.direction == OUT:
            statemgr.switch(self.new_state, *self.args, **self.kwargs)
            statemgr.next_transition()
        elif self.direction == IN:
            statemgr.next_transition()

    def draw(self, surface):
        surface.fill((0,0,0))


class Fade(Transition):
    def __init__(self, new_state, direction, transition_delay=500, transition_color=(0,0,0), *args, **kwargs):
        super(Fade, self).__init__(new_state, direction, *args, **kwargs)
        self.max_delay = float(transition_delay)
        self.delay = transition_delay
        self.color = transition_color
        self.surface = pygame.Surface((metrics.SCREEN_WIDTH, metrics.SCREEN_HEIGHT), pygame.HWSURFACE | pygame.SRCALPHA)
        if direction == OUT:
            self.surface.fill((0,0,0,0))
        else:
            self.surface.fill(self.color)


    def update(self, td):
        self.delay -= td

        if self.delay < 0:
            if self.direction == OUT:
                statemgr.switch(self.new_state, *self.args, **self.kwargs)
                statemgr.next_transition()
            elif self.direction == IN:
                statemgr.next_transition()
        else:
            if self.direction == OUT:
                self.surface.fill(self.color + (int(((self.max_delay-self.delay) / self.max_delay)*255),))
            else:
                self.surface.fill(self.color + (int((self.delay / self.max_delay)*255),))


    def draw(self, surface):
        surface.blit(self.surface, (0,0))


class Spotlight(Transition):
    def __init__(self, new_state, direction, transition_delay=500, transition_color=(0,0,0), *args, **kwargs):
        super(Spotlight, self).__init__(new_state, direction, *args, **kwargs)
        self.max_delay = float(transition_delay)
        self.delay = transition_delay
        self.color = transition_color
        self.surface = pygame.Surface((metrics.SCREEN_WIDTH, metrics.SCREEN_HEIGHT), pygame.HWSURFACE | pygame.SRCALPHA)
        if direction == OUT:
            self.surface.fill((0,0,0,0))
        else:
            self.surface.fill(self.color)
        self.max_size = math.hypot(metrics.SCREEN_HEIGHT / 2, metrics.SCREEN_WIDTH / 2)

    def update(self, td):
        self.delay -= td

        if self.delay < 0:
            if self.direction == OUT:
                statemgr.switch(self.new_state, *self.args, **self.kwargs)
                statemgr.next_transition()
            elif self.direction == IN:
                statemgr.next_transition()
        else:
            if self.direction == OUT:
                self.surface.fill(self.color)
                pygame.draw.circle(self.surface, (0,0,0,0), self.surface.get_rect().center, int((self.delay/self.max_delay) * self.max_size))
            else:
                self.surface.fill(self.color)
                pygame.draw.circle(self.surface, (0,0,0,0), self.surface.get_rect().center, int(((self.max_delay-self.delay) / self.max_delay) * self.max_size))


    def draw(self, surface):
        surface.blit(self.surface, (0,0))
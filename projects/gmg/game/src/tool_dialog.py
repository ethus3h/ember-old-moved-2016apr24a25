#!/usr/bin/env python

"""
Tool for viewing dialog boxes
"""

import pygame
import sys
import dialog
import metrics

pygame.init()

disp = pygame.display.set_mode((metrics.SCREEN_WIDTH, metrics.SCREEN_HEIGHT))
clock = pygame.time.Clock()

d = dialog.Dialog(sys.argv[1])

keep_going = True
while keep_going:
    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            keep_going = False

        if event.type == pygame.KEYDOWN:
            d.next()

    td = clock.tick(metrics.FPS)

    d.update(td)
    disp.fill((128,128,128))
    d.draw(disp)
    pygame.display.flip()

    if d.is_done:
        keep_going = False

#!/usr/bin/env python

"""
Tool for viewing animations
"""

import pygame
import sys
import assets
import animation
import metrics

pygame.init()

disp = pygame.display.set_mode((metrics.SCREEN_WIDTH, metrics.SCREEN_HEIGHT))
clock = pygame.time.Clock()

anim = assets.getSpriteAnim(sys.argv[1])
sequences = anim.sequences.keys()
current = 0
cursor = animation.SimpleCursor()
cursor.play(anim.getSequence(sequences[current]))

font = assets.getFont(None, 10)
anim_text = font.render(str(current) + ": " + sequences[current], False, (0,0,0))

keep_going = True
while keep_going:
    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            keep_going = False

        if event.type == pygame.KEYDOWN:
            current = (current+1) % len(sequences)
            anim_text = font.render(str(current) + ": " + sequences[current], False, (0,0,0))
            cursor.play(anim.getSequence(sequences[current]))

    td = clock.tick(metrics.FPS)
    cursor.update(td)

    disp.fill((255,255,255))
    disp.blit(cursor.frame, (5,5))
    disp.blit(anim_text, (300, 5))
    pygame.display.flip()

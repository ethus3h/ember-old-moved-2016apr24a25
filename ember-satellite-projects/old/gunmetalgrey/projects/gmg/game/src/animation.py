#!/usr/bin/env python

"""Animation file format, right now, is a text file that looks like this:
{"image":"filename.png",
"columns":8,
"rows":8,
"looping":true,
frames:[
    (frame #, duration),
    ...
]
}"""

# Todo: Unit tests
# Todo: Exception handling
# Todo: Logging

import json
import assets

class Sequence:
    def __init__(self, looping=False, frames = []):
        self.looping = looping
        self.frames = frames

class Animation:
    """Describes an animation sequence"""
    def __init__(self):
        # Load animation from file
        self.sequences = {}

    def loadSpriteAnim(self, filename):
        """Loads sprite animation from file."""
        self.sequences = {}
        file = assets.load(filename)
        tmp = json.load(file)
        file.close()

        # Graphics for animation
        img_list = assets.getImageList(tmp["image"], tmp["columns"], tmp["rows"])

        # Create the animation sequences from the file's data
        for sequence in tmp["sequences"]:
            frames = []
            for cell,duration in sequence["frames"]:
                frames.append((img_list[cell], duration))
            self.sequences[sequence["name"]] = Sequence(sequence["looping"], frames)

    def getSequence(self, name):
        """Gets the specified animation sequence if it exists, else return broken animation animation"""
        seq = self.sequences.get(name)
        if seq:
            return seq
        return assets.getSpriteAnim("default/anim.json").getSequence("default")

class Cursor(object):
    """Keeps track of animation playback"""
    def __init__(self):
        super(Cursor, self).__init__()

        self.animation = None
        self.frame_number = 0
        self.time_to_next = 0
        self.frame = None
        self.playing = True

    def play(self, animation, reset=True):
        """Play the specified animation.  If reset is true, it will start at frame 0, else it will play from current frame"""
        self.animation = animation
        if reset:
            self.frame_number = 0
            self.time_to_next = animation.frames[0][1]
            self.frame = animation.frames[0][0]
        else:
            # This allows for animations to start playing in sync with the previously playing animation
            self.frame_number = self.frame_number % len(animation.frames)
            self.frame = animation.frames[self.frame_number][0]
        self.playing = True

    def update(self, td):
        """Please overload with correct playback function"""
        return False


class SimpleCursor(Cursor):
    """Keeps track of animation playback"""
    def __init__(self):
        super(SimpleCursor, self).__init__()

    def update(self, td):
        """Non-interpolated animation cursor.  It puts current frame in self.frame."""
        self.time_to_next -= td
        if self.playing and self.time_to_next <= 0:
            # Go to the next frame
            self.frame_number += 1
            if self.frame_number >= len(self.animation.frames):
                # At the end of the animation sequence
                if self.animation.looping:
                    # Loop back to start
                    self.frame_number = 0
                else:
                    # Stop at last frame
                    self.frame_number = len(self.animation.frames) - 1
                    self.playing = False

            # Get current frame information (frame and delay)
            self.frame, delay = self.animation.frames[self.frame_number]

            # How long should be waited before going to the next frame
            self.time_to_next += delay

        return self.playing


class InterpolatedCursor(Cursor):
    """Interpolates numerical values in an iterable object.  Can be used for paths or blending between colors, for example."""
    def __init__(self):
        super(InterpolatedCursor, self).__init__()
        self.current_frame = None
        self.next_frame = None
        self.frame_delay = 0.0

    def play(self, animation, reset=True):
        super(InterpolatedCursor, self).play(animation, reset)
        self.current_frame = self.frame
        self.next_frame = animation.frames[1 % len(animation.frames)][0]
        self.frame_delay = float(self.time_to_next)

    def update(self, td):
        """Animates with interpolating values.  Good for paths."""
        self.time_to_next -= td
        if self.playing and self.time_to_next <= 0:
            # Go to next frame
            self.frame_number += 1
            if self.frame_number >= len(self.animation.frames):
                # At the end of the animation sequence
                if self.animation.looping:
                    self.frame_number = 0
                else:
                    self.frame_number = len(self.animation.frames) - 1
                    self.playing = False

            self.current_frame, delay = self.animation.frames[self.frame_number]
            self.time_to_next += delay
            self.frame_delay = float(delay)
            self.next_frame = self.animation.frames[(self.frame_number + 1) % len(self.animation.frames)][0]

        # Amount the animation is between the current frame and the next frame.
        interpolation = self.time_to_next / self.frame_delay

        # Set the frame to the interpolated values between the key frame values
        self.frame = [self.current_frame[i] * interpolation + self.next_frame[i] * (1.0 - interpolation) for i in xrange(len(self.current_frame))]

        return self.playing

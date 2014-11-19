#!/usr/bin/env python

"""
Simple Publisher / Subscriber classes.
"""

# TODO: Proper unit testing

class Publisher:
    """A Publisher sends messages to all the Subscribers that are subscribed to it"""
    def __init__(self):
        self.subscriptions = []

    def subscribe(self, subscriber):
        self.subscriptions.append(subscriber)

    def unsubscribe(self, subscriber):
        self.subscriptions.remove(subscriber)

    def post(self, msg, *args, **kwargs):
        for subscriber in self.subscriptions:
            subscriber.receiveMessage(msg, *args, **kwargs)


class Subscriber:
    """Subscribers can subscribe to Publishers and receive messages."""
    def __init__(self):
        self.messages = []

    def receiveMessage(self, msg, *args, **kwargs):
        self.messages.append((msg, args, kwargs))

    def get(self):
        """A generator allowing iterating through messages then clearing the message queue"""
        for message in self.messages:
            yield message
        self.messages = []


if __name__ == "__main__":
    # Running this directly will test the Publisher and Subscriber classes
    p = Publisher()
    s = Subscriber()

    p.subscribe(s)

    p.post("Hello")
    p.post("World", 123)

    for m in s.get():
        print m

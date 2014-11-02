package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Common.Process.EmberProcess;

/**
 * Created by elliot on 14.11.01.
 */
public class Greeter implements EmberProcess {
    @Override
    public void run() {
        System.out.println("Hello, World!");
    }

    @Override
    public void processSignalHandler(Integer signal) {

    }

    @Override
    public void pause() {

    }

    @Override
    public void resume() {

    }

    @Override
    public void terminate() {

    }

    @Override
    public void kill() {

    }
}

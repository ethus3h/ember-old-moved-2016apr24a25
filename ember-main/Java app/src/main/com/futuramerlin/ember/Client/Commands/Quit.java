package com.futuramerlin.ember.Client.Commands;

import com.futuramerlin.ember.Client.Session.Session;
import com.futuramerlin.ember.Common.Process.EmberProcess;

/**
 * Created by elliot on 14.11.28.
 */
public class Quit implements EmberProcess {

    private String[] args;
    private String command;
    private Session session;

    @Override
    public void processSignalHandler(Integer signal) {
    }

    @Override
    public void run() {

    }

    @Override
    public void start(String cmd, Session s, String... args) {
        this.command = cmd;
        this.session = s;
        this.args = args;
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

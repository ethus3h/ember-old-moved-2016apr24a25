package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Common.Exception.NoContextsFoundException;
import com.futuramerlin.ember.Common.Process.EmberProcess;

import java.util.ArrayList;

/**
 * Created by elliot on 14.11.27.
 */
public class SessionCreator implements EmberProcess {
    public ArrayList<Session> sessions = null;
    SessionCreator() throws NoContextsFoundException {
        this.getContexts();
    }

    public void getContexts() throws NoContextsFoundException {
        if(System.console() != null) {
            this.sessions.add(new Session().make(new Context("terminal")));
        }
        if(this.sessions.size() == 0)
        {
            throw new NoContextsFoundException();
        }
    }

    @Override
    public void processSignalHandler(Integer signal) {

    }

    @Override
    public void run() {

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

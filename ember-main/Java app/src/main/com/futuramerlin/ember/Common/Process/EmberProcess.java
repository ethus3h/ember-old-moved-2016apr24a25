package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Client.Session.Session;

/**
 * Created by elliot on 14.11.01.
 */
public interface EmberProcess {
    public String state = "running";
    /**
     * Send a signal to the process.
     */
    public void processSignalHandler(Integer signal);

    /**
     * Creating the object, for compatibility with Runnable
     */
    public void run();

    /**
     * Tell the process to start whatever it's supposed to do.
     */
    public void start(String cmd, Session s, Object... args) throws Exception;
    /**
     * Prepare for temporary suspension of execution.
     */
    void pause();

    /**
     * Recover after suspension of execution.
     */
    void resume();

    /**
     * Stop execution gracefully
     */
    void terminate();

    /**
     * Stop execution immediately
     */
    void kill();
}

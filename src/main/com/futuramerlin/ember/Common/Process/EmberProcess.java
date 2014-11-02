package com.futuramerlin.ember.Common.Process;

/**
 * Created by elliot on 14.11.01.
 */
public interface EmberProcess {
    /**
     * Send a signal to the process.
     */
    public void processSignalHandler(Integer signal);

    /**
     * Tell the process to start whatever it's supposed to do.
     */
    public void run();

    /**
     * Temporarily suspend execution
     */
    void pause();

    /**
     * Resume execution if suspended
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

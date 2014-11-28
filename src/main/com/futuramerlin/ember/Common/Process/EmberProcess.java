package com.futuramerlin.ember.Common.Process;

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
     * Tell the process to start whatever it's supposed to do.
     */
    public void run();

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

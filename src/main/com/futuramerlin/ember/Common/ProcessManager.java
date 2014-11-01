package com.futuramerlin.ember.Common;

/**
 * Created by elliot on 14.11.01.
 */
public class ProcessManager {
    public Integer getNewestPid = -1;

    public void start(String c) {
        EmberProcess p = new EmberProcess(this, c);
    }
    public void start(String c, String... args) {
        EmberProcess p = new EmberProcess(this, c, args);
    }
}

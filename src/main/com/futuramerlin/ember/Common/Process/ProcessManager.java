package com.futuramerlin.ember.Common.Process;

import java.util.ArrayList;

/**
 * Created by elliot on 14.11.01.
 */
public class ProcessManager {
    public Integer newestPid = -1;
    private ArrayList<EmberProcessInstance> processes = new ArrayList<EmberProcessInstance>();

    public EmberProcessInstance start(String c) throws Exception {
        return start(c,null);
    }
    public EmberProcessInstance start(String c, String... args) throws Exception {
        EmberProcessInstance p = new EmberProcessInstance(this, c,args);
        this.processes.add(p);
        p.pid = this.processes.indexOf(p);
        this.newestPid = p.pid;
        return p;
    }

    public Integer getNewestPid() {
        return this.newestPid;
    }
}

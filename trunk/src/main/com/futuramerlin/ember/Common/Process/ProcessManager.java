package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Common.Exception.classNotRunnableException;

import java.util.ArrayList;

/**
 * Created by elliot on 14.11.01.
 */
public class ProcessManager {
    public Integer newestPid = -1;
    private ArrayList<EmberProcessInstance> processes = new ArrayList<EmberProcessInstance>();
    public EmberProcessInstance start(String c) throws Exception {
        return start(c,(String[])null);
    }
    public EmberProcessInstance start(String c, String... args) throws Exception {
        this.checkRunnable(Class.forName("com.futuramerlin.ember."+c));
        EmberProcessInstance p = new EmberProcessInstance(this, c,args);
        this.processes.add(p);
        p.pid = this.processes.indexOf(p);
        this.newestPid = p.pid;
        return p;
    }

    public Integer getNewestPid() {
        return this.newestPid;
    }

    public void checkRunnable(Class c) throws classNotRunnableException {
        if(!EmberProcess.class.isAssignableFrom(c)) {
            throw new classNotRunnableException();
        }
    }
}

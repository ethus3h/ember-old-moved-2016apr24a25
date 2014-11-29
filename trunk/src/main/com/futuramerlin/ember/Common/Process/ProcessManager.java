package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Client.CommandProcessor;
import com.futuramerlin.ember.Client.Session.Session;
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
        return this.start(c,(Session)null,args);
    }

    public Integer getNewestPid() {
        return this.newestPid;
    }

    public void checkRunnable(Class c) throws classNotRunnableException {
        if(!EmberProcess.class.isAssignableFrom(c)) {
            throw new classNotRunnableException();
        }
    }

    public EmberProcessInstance start(String cmd, Session s, String... args) throws Exception {
        this.checkRunnable(Class.forName("com.futuramerlin.ember."+cmd));
        EmberProcessInstance p = new EmberProcessInstance(s,cmd,args);
        this.processes.add(p);
        p.pid = this.processes.indexOf(p);
        this.newestPid = p.pid;
        return p;
    }
}

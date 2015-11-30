package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Common.Exception.classNotRunnableException;
import org.junit.Test;

/**
 * Created by elliot on 14.11.01.
 */
public class ProcessManagerTest {
    @Test
    public void testCreateProcessManager() throws Exception {
        ProcessManager p = new ProcessManager();

    }

    @Test
    public void testAddProcess() throws Exception {
        ProcessManager p = new ProcessManager();
        p.start("Common.Process.Greeter");
    }
    @Test
    public void testAddProcessWithArg() throws Exception {
        ProcessManager p = new ProcessManager();
        p.start("Common.Process.Greeter","Doom");
    }
    @Test
    public void testGetNewestPid() throws Exception {
        ProcessManager p = new ProcessManager();
        org.junit.Assert.assertTrue(p.getNewestPid() instanceof Integer);
        org.junit.Assert.assertTrue(p.getNewestPid() == -1);
    }
    @Test
    public void testGetNewestPidAfter() throws Exception {
        ProcessManager p = new ProcessManager();
        p.start("Common.Process.Greeter","Doom");
        org.junit.Assert.assertTrue(p.getNewestPid() instanceof Integer);
        org.junit.Assert.assertTrue(p.getNewestPid() == 0);
    }

    @Test
    public void testIsRunnable() throws Exception {
        ProcessManager p = new ProcessManager();
        p.checkRunnable(Greeter.class);
    }
    @Test(expected=classNotRunnableException.class)
    public void testAddProcessNotRunnable() throws Exception {
        ProcessManager p = new ProcessManager();
        p.start("Common.Process.ProcessManager");
    }
}

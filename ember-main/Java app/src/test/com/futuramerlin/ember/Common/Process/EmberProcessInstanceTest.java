package com.futuramerlin.ember.Common.Process;

import org.junit.Test;

/**
 * Created by elliot on 14.11.01.
 */
public class EmberProcessInstanceTest {
    @Test
    public void testCreateProcess() throws Exception {
        String[] args = null;
        ProcessManager e = new ProcessManager();
        EmberProcessInstance a = e.start("Common.Process.Greeter",args);
        org.junit.Assert.assertTrue(a.pid instanceof Integer);
    }
    @Test
    public void testCreateProcessNoArgs() throws Exception {
        ProcessManager e = new ProcessManager();
        EmberProcessInstance a = e.start("Common.Process.Greeter");
        org.junit.Assert.assertTrue(a.pid instanceof Integer);
    }
}

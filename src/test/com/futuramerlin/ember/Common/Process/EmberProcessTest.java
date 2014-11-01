package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Common.EmberProcess;
import com.futuramerlin.ember.Common.ProcessManager;
import org.junit.Test;

/**
 * Created by elliot on 14.11.01.
 */
public class EmberProcessTest {
    @Test
    public void testCreateProcess() throws Exception {
        String c = "Greeter";
        String[] args = null;
        ProcessManager e = new ProcessManager();
        EmberProcess a = new EmberProcess(e, c, args);
        org.junit.Assert.assertTrue(a.pid instanceof Integer);
    }
    @Test
    public void testCreateProcessNoArgs() throws Exception {
        String c = "Greeter";
        ProcessManager e = new ProcessManager();
        EmberProcess a = new EmberProcess(e, c);
        org.junit.Assert.assertTrue(a.pid instanceof Integer);
    }
}

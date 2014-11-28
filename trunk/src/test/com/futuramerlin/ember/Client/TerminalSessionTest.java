package com.futuramerlin.ember.Client;

import org.junit.Test;

/**
 * Created by elliot on 14.11.27.
 */
public class TerminalSessionTest {
    @Test
    public void testCreateSession() throws Exception {
        TerminalSession t = new TerminalSession();

    }
    @Test
    public void testHasProcessor() throws Exception {
        TerminalSession t = new TerminalSession();
        org.junit.Assert.assertNotNull(t);

    }
}

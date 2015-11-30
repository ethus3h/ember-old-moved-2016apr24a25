package com.futuramerlin.ember.Client;

import org.junit.Test;

/**
 * Created by elliot on 14.11.27.
 */
public class ContextTest {

    @Test
    public void testCreateContext() throws Exception {
        Context p = new Context();

    }

    @Test
    public void testCreateContextTerminal() throws Exception {
        Context p = new Context("terminal");

    }
}

package com.futuramerlin.ember.Client;

import org.junit.Test;

/**
 * Created by elliot on 14.11.27.
 */
public class CommandProcessorTest {

    @Test
    public void testCreateCommandProcessor() throws Exception {
        CommandProcessor p = new CommandProcessor();

    }

    @Test
    public void testCommandRq() throws Exception {
        CommandProcessor p = new CommandProcessor();
        p.command("");

    }
}

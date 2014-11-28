package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.Session.Session;
import org.junit.Test;

/**
 * Created by elliot on 14.11.27.
 */
public class CommandProcessorTest {

    @Test
    public void testCreateCommandProcessor() throws Exception {
        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);

    }

    @Test
    public void testCommandRq() throws Exception {
        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);
        p.command("");

    }
}

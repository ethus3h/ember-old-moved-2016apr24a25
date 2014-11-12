package com.futuramerlin.ember.Common.IO;

import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import org.junit.Test;

/**
 * Created by elliot on 14.11.11.
 */
public class ConsoleInteractionBuilderTest {
    @Test
    public void testCreateConsoleInteractionBuilder() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();

    }

    @Test
    public void testGetTerminal() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.testForTerminal();


    }

    @Test
    public void testTerminalValueReturned() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.testForTerminal();
        org.junit.Assert.assertEquals(b.hasTerminal, false);

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testStart() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.start();
    }

    @Test
    public void testSay() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.say("Hello, World!");

    }
    @Test(expected=NoTerminalFoundException.class)
    public void testAsk() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.ask("Hello, World? ");

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testGetYes() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.getYes();

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testGetNo() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.getYes();

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testGetInputTraits() throws Exception {
        ConsoleInteractionBuilder b = new ConsoleInteractionBuilder();
        b.getInputTraits();

    }
}

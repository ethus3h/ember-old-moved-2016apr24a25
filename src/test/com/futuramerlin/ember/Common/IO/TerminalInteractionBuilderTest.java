package com.futuramerlin.ember.Common.IO;

import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import org.junit.Test;

import java.io.Console;

/**
 * Created by elliot on 14.11.11.
 */
public class TerminalInteractionBuilderTest {
    @Test
    public void testCreateConsoleInteractionBuilder() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();

    }

    @Test
    public void testGetTerminal() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.testForTerminal();


    }

    @Test
    public void testTerminalValueReturned() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.testForTerminal();
        org.junit.Assert.assertEquals(b.hasTerminal, false);

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testStart() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.start();
    }

    @Test
    public void testSay() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.say("Hello, World!");

    }
    @Test(expected=NoTerminalFoundException.class)
    public void testAsk() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.ask("Hello, World? ");

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testGetYes() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.getYes();

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testGetNo() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.getYes();

    }

    @Test(expected=NoTerminalFoundException.class)
    public void testGetInputTraits() throws Exception {
        TerminalInteractionBuilder b = new TerminalInteractionBuilder();
        b.getInputTraits();

    }
}

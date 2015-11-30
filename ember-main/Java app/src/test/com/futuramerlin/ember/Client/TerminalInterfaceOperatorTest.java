package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.Session.Session;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import org.junit.Rule;
import org.junit.Test;
import org.junit.contrib.java.lang.system.StandardOutputStreamLog;

/**
 * Created by elliot on 14.11.27.
 */
public class TerminalInterfaceOperatorTest {

    @Rule
    public final StandardOutputStreamLog log = new StandardOutputStreamLog();
    @Test
    public void testCommand() throws Exception {
        Session b = new Session().make(new Context("terminal"));
        TerminalInterfaceOperator c = new TerminalInterfaceOperator(b);
        c.session.commandProcessor.command("");

    }
    @Test
    public void testProcessInput() throws Exception {
        Session b = new Session().make(new Context("terminal"));
        TerminalInterfaceOperator c = new TerminalInterfaceOperator(b);
        c.processInput();
        org.junit.Assert.assertTrue(log.getLog().startsWith("No terminal was found. Please only use TerminalInterfaceOperator when a terminal is available.\n"));

    }

    @Test
    public void testInteractOnTerminal() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Session b = new Session().make(new Context("terminal"));
        TerminalInterfaceOperator c = new TerminalInterfaceOperator(b);
        c.interactOnTerminal();


    }
    @Test
         public void testTerminalInterfaceOperatorHasTerm() throws Exception {
        Session b = new Session().make(new Context("terminal"));
        TerminalInterfaceOperator c = new TerminalInterfaceOperator(b);
        //Won't have a console while running the tests.
        org.junit.Assert.assertNull(c.term);
    }

    @Test(expected=NoTerminalFoundException.class)
    public void testTerminalInterfaceOperatorWaitForInput() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        //Won't have a console while running the tests.
        Session b = new Session().make(new Context("terminal"));
        TerminalInterfaceOperator c = new TerminalInterfaceOperator(b);
        c.waitForInput();
    }

    @Test
    public void testStop() throws Exception {
        Session b = new Session().make(new Context("terminal"));
        TerminalInterfaceOperator c = new TerminalInterfaceOperator(b);
        c.interactOnTerminal();
        b.running = false;

    }

    @Test
    public void testQuit() throws Exception {
        Session b = new Session().make(new Context("terminal"));
        TerminalInterfaceOperator c = new TerminalInterfaceOperator(b);
        c.command("quit");

    }
}

package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.Session.Session;
import com.futuramerlin.ember.Common.DataProcessor.StringProcessor;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;

import java.io.Console;

/**
 * Created by elliot on 14.11.27.
 */
public class TerminalInterfaceOperator {
    public Console term;
    Session session;

    public TerminalInterfaceOperator(Session s) {
        this.session = s;
    }


    public void interactOnTerminal() {
        while (this.session.bootstrapper.running) {
            this.processInput();
        }
    }

    public void processInput() {
        try {
            this.session.commandProcessor.command(this.waitForInput());
        }
        //This would presumably need a mock to test
        catch(ZeroLengthInputException e) {
            System.out.println("For help, type \"help\", and press the Enter key.");
        }
        catch (NoTerminalFoundException e) {
            System.out.println("No terminal was found. Please only use TerminalInterfaceOperator when a terminal is available.");
            this.session.bootstrapper.running = false;
            //Not sure how to test exiting this catch block
        }
    }
    public String waitForInput() throws ZeroLengthInputException, NoTerminalFoundException {
        StringProcessor p = new StringProcessor();
        if(this.term == null) {
            throw new NoTerminalFoundException();
        }
        //This would presumably need a mock to test
        return this.term.readLine("$ ");
    }


    public void command(String c) {
        this.session.commandProcessor.command(c);
    }
}

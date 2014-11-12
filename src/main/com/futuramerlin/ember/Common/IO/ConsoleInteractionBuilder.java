package com.futuramerlin.ember.Common.IO;

import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;

import java.io.Console;

/**
 * Created by elliot on 14.11.12.
 * Talk to a user on System.console and get yes/no input working
 */
public class ConsoleInteractionBuilder {

    public boolean hasTerminal;
    private Console term;
    private String yes;
    private String no;

    void getYes() throws NoTerminalFoundException {
        this.yes = this.ask("What key will you use to say \"Yes\"? ");
    }

    private void getNo() throws NoTerminalFoundException {
        this.no=this.ask("What key will you use to say \"No\"? ");
    }

    public void testForTerminal() {
        this.term = System.console();
        if(this.term == null) {
            this.hasTerminal = false;
        }
        else {
            this.hasTerminal = true;
        }
    }


    public void start() throws NoTerminalFoundException {
        this.testForTerminal();
        this.getInputTraits();

    }

    public void getInputTraits() throws NoTerminalFoundException {
        if(this.hasTerminal) {
            this.getYes();
            this.getNo();
        }
        else {
            throw new NoTerminalFoundException();
        }
    }

    public void say(String s) {
        System.out.println(s);
    }

    public String ask(String q) throws NoTerminalFoundException {
        if (this.hasTerminal) {
            return this.term.readLine(q);
        }
        throw new NoTerminalFoundException();
    }
}

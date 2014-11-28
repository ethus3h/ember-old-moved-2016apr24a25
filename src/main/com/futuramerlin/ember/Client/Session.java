package com.futuramerlin.ember.Client;

/**
 * Created by elliot on 14.11.27.
 */
public class Session {
    Bootstrapper bootstrapper;
    public CommandProcessor commandProcessor;
    public boolean running = true;

    public Session() {
        this.bootstrapper = new Bootstrapper();
        this.commandProcessor = new CommandProcessor();
    }
    public Session make(Context c) {
        return new TerminalSession();
    }

}

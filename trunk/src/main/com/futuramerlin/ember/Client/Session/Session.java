package com.futuramerlin.ember.Client.Session;

import com.futuramerlin.ember.Client.Bootstrapper;
import com.futuramerlin.ember.Client.CommandProcessor;
import com.futuramerlin.ember.Client.Context;
import com.futuramerlin.ember.Common.Exception.UnknownSessionTypeException;

/**
 * Created by elliot on 14.11.27.
 */
public class Session {
    public Bootstrapper bootstrapper;
    public CommandProcessor commandProcessor;
    public boolean running = true;
    public Context context;

    public Session() {
        this.bootstrapper = new Bootstrapper();
        this.commandProcessor = new CommandProcessor(this);
    }
    public Session make(Context c) throws UnknownSessionTypeException {
        if(c.context == "terminal") {
            return new TerminalSession();
        }
        else if(c.context == "null") {
            return new NullSession();
        }
        throw new UnknownSessionTypeException();
    }

}

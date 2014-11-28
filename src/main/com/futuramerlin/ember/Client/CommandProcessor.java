package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.Session.Session;

/**
 * Created by elliot on 14.11.27.
 */
public class CommandProcessor {
    private Session session;

    public CommandProcessor(Session s) {
        this.session = s;
    }

    public void command(String c) {
        if (c.equals("quit")) {
            Session s = this.session;
            s.running = false;
        }
    }
}

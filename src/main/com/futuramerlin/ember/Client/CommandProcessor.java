package com.futuramerlin.ember.Client;

/**
 * Created by elliot on 14.11.27.
 */
public class CommandProcessor {
    private Session session;

    public void command(String c) {
        if (c.equals("quit")) {
            Session s = this.session;
            s.running = false;
        }
    }
}

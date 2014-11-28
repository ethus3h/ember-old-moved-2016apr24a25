package com.futuramerlin.ember.Client;


import com.futuramerlin.ember.Common.Exception.NoContextsFoundException;
import com.futuramerlin.ember.Common.Process.ProcessManager;

/**
 * Created by elliot on 14.10.29.
 * System requirements:
 * Java 8
 * A terminal or terminal emulator that Java recognizes as System.console and can read input from (this is necessary for setup, but eventually should not be needed for running Ember once it has a GUI)
 * Persistent storage, if you want to save config files
 * Write access to ~/.ember, if you want to save config files
 * Permission to listen to a socket, if you want to provide a Web server
 */
public class Client {

    public ProcessManager p;

    public static void main(String[] args) throws Exception {
        Client c = new Client();
        c.begin();
    }

    void begin() throws Exception {
        System.out.println("Hello! Ember is starting now. Please wait; it may take a little while...");
        this.p = new ProcessManager();
        try {
            p.start("Client.Session.SessionCreator");
        }
        catch(NoContextsFoundException e) {
            System.out.println("It doesn't look like you're using Ember in a context in which you can give it commands. Presumably in a later version, a scriptable interface will be available.");
        }
    }
}

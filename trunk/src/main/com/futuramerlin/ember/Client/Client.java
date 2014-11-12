package com.futuramerlin.ember.Client;


import com.futuramerlin.ember.Common.Process.ProcessManager;
import com.futuramerlin.ember.Client.Bootstrapper;

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

    public static void main(String[] args) throws Exception {
        Client c = new Client();
        c.begin();

    }

    private void begin() throws Exception {
        System.out.println("Hello! Ember is starting now. Please wait; it may take a little while...");
        ProcessManager p = new ProcessManager();
        p.start("Client.Bootstrapper");
    }

}

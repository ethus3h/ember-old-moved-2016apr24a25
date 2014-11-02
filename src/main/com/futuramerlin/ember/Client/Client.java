package com.futuramerlin.ember.Client;


import com.futuramerlin.ember.Common.Process.ProcessManager;
import com.futuramerlin.ember.Client.Bootstrapper;

/**
 * Created by elliot on 14.10.29.
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

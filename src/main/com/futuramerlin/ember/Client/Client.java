package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.ApiClient.ApiClient;
import com.futuramerlin.ember.Common.DataProcessor.StringProcessor;
import com.futuramerlin.ember.Common.Exception.TerminalNotFound;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import com.futuramerlin.ember.Common.Process.ProcessManager;

import java.io.Console;

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

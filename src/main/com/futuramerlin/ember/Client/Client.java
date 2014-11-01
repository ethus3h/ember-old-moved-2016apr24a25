package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.ApiClient.ApiClient;
import com.futuramerlin.ember.DataProcessor.StringProcessor;
import com.futuramerlin.ember.Throwable.TerminalNotFound;
import com.futuramerlin.ember.Throwable.ZeroLengthInputException;

import java.io.Console;

/**
 * Created by elliot on 14.10.29.
 */
public class Client {
    public ApiClient apiClient;
    public Console term;
    private String context;
    private boolean running;

    public static void main(String[] args) throws ZeroLengthInputException, TerminalNotFound {
        Client c = new Client();
        c.start();
        c.run();
        c.stop();
    }

    public Client() {
        //System.out.println("Doom");
        this.term = System.console();
        this.apiClient = this.getApiClient();
    }

    public ApiClient getNewApiClient() {
        return new ApiClient();
    }

    public ApiClient getApiClient() {
        if(this.apiClient == null) {
            return new ApiClient();
        }
        return this.apiClient;
    }

    public String waitForInput() throws ZeroLengthInputException, TerminalNotFound {
        if(this.term != null) {
            StringProcessor p = new StringProcessor();
                return this.term.readLine("$ ");
        }
        throw new TerminalNotFound();
    }

    public String getContext() {
        if(this.term != null) {
            this.context = "terminal";
        }
        else {
            this.context = null;
        }
        return context;
    }

    public void run() throws ZeroLengthInputException, TerminalNotFound {
        if(this.context == null) {
            this.printNullContextMessage();
        }
        if(this.context == "terminal") {
            this.interactOnTerminal();
        }
    }

    public void interactOnTerminal() throws ZeroLengthInputException, TerminalNotFound {
        while (this.running) {
            this.processInput();
        }
    }

    public void printNullContextMessage() {
        System.out.println("It doesn't look like you're communicating with Ember in a context that it understands. Presumably in a later version, a scriptable interface will be available.");
    }

    public void start() {
        System.out.println("Hello! Please wait; it may take a little while...");
        this.getApiClient();
        this.getContext();
        this.running = true;
        this.message("Ember has started, and is ready to use.");
    }

    public void stop() {
        this.message("Ember is stopping now. Please wait; it may take a little while...");
        this.message("Ember has stopped.");
    }

    public void command(String c) {
        //System.out.println(c);
        //System.out.println(this.running);
        //System.out.println(c.equals("quit"));
        if (c.equals("quit")) {
            //System.out.println("QUITTING");
            this.running = false;
        }
    }

    public void processInput() throws ZeroLengthInputException, TerminalNotFound {
        String c = this.waitForInput();
        this.command(c);
    }

    public void message(String s) {
        if((this.context == null) || (this.context == "terminal")) {
            System.out.println(s);
        }
    }
}

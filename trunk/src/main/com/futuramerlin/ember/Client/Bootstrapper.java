package com.futuramerlin.ember.Client;


import com.futuramerlin.ember.Client.ApiClient.ApiClient;
import com.futuramerlin.ember.Common.DataProcessor.StringProcessor;
import com.futuramerlin.ember.Common.Exception.ApiClientAlreadyExistsException;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import com.futuramerlin.ember.Common.Process.EmberProcess;

import java.io.Console;

/**
 * Created by elliot on 14.11.01.
 */
public class Bootstrapper implements EmberProcess {
    public ApiClient apiClient;
    String context;
    public boolean running;


    @Override
    public void processSignalHandler(Integer signal) {

    }

    @Override
    public void run() {
        this.start();

        //this.operate();
        this.stop();
    }

    @Override
    public void pause() {

    }

    @Override
    public void resume() {

    }

    @Override
    public void terminate() {

    }

    @Override
    public void kill() {

    }

    public Bootstrapper() {
        this.apiClient = this.getApiClient();
    }
    public void start() {
        this.apiClient = this.getApiClient();
        this.getContext();
        this.running = true;
        this.message("Ember has started, and is ready to use.");
    }
//    public void operate() {
//        if(this.context == null) {
//            this.printNullContextMessage();
//        }
//        if(this.context == "terminal") {
//            new TerminalInterfaceOperator(this).interactOnTerminal();
//        }
//    }
    public void stop() {
        this.message("Ember is stopping now. Please wait; it may take a little while...");
        this.message("Ember has stopped.");
    }

    public ApiClient getNewApiClient() throws ApiClientAlreadyExistsException {
        if(this.apiClient==null) {
            this.apiClient = new ApiClient();
        }
        else {
            throw new ApiClientAlreadyExistsException();
        }
        return this.apiClient;
    }

    public ApiClient getApiClient() {
        if(this.apiClient == null) {
            try {
                this.getNewApiClient();
            }
            catch(ApiClientAlreadyExistsException e) {
                //This block should be tested by testCatchApiClientAlreadyExists(). See https://github.com/stefanbirkner/system-rules/issues/4 for a possible explanation.
                System.out.println("Failed to create: ApiClient already exists.");
            }
        }
        return this.apiClient;
    }


    public String getContext() {
        if(System.console() != null) {
            this.context = "terminal";
        }
        else {
            this.context = null;
        }
        return context;
    }


    public void printNullContextMessage() {
        System.out.println("It doesn't look like you're using Ember in a context in which you can give it commands. Presumably in a later version, a scriptable interface will be available.");
    }



    public void message(String s) {
        if((this.context == null) || (this.context == "terminal")) {
            System.out.println(s);
        }
    }

    public String[] listInteractionContexts() {
        return new String[]{};
    }
}

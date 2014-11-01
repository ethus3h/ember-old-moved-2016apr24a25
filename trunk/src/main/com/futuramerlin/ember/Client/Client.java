package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.ApiClient.ApiClient;

import java.io.BufferedReader;
import java.io.Console;

/**
 * Created by elliot on 14.10.29.
 */
public class Client {
    public ApiClient apiClient;
    public Console term;
    private String context;

    public static void main(String[] args) {
        Client c = new Client();
        c.sayHello();
        c.waitForInput();
    }

    public Client() {
        //System.out.println("Doom");
        this.term = System.console();
        this.apiClient = this.getApiClient();
    }

    public void sayHello() {
        System.out.println("Hello! Ember is now starting. It may take a little while for it to do so.");
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

    public String waitForInput() {
        if(this.term != null) {
            return this.term.readLine("$ ");
        }
        return null;
    }

    public String getContext() {
        if(this.term != null) {
            this.context = "terminal";
        }
        else {
            this.context = "null";
        }
        return context;
    }
}

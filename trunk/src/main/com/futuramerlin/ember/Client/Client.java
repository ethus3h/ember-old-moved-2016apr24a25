package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.ApiClient.ApiClient;

/**
 * Created by elliot on 14.10.29.
 */
public class Client {
    public ApiClient apiClient;

    public static void main(String[] args) {
        Client c = new Client();
        c.sayHello();
        //c.getNewApiClient();
    }
    public void sayHello() {
        System.out.println("Hello! Ember is now starting. It may take a little while for it to do so.");
    }
/*
    public ApiClient getNewApiClient() {
        return new ApiClient();
    }

    public ApiClient getApiClient() {
        if(this.apiClient == null) {
            return new ApiClient();
        }
        return this.apiClient;
    } */
}

package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.ApiClient.ApiClient;
import com.futuramerlin.ember.Client.Client;
import org.junit.Test;
import org.junit.Assert;

/**
 * Created by elliot on 14.10.29.
 */
public class ClientTest {
    public static void main(String[] args) throws Exception {
        ClientTest c = new ClientTest();
        c.testClientGetNewApiClientAssigns();
    }
    @Test
    public void testCreateStdioClient() throws Exception {
        Client c = new Client();

    }

    @Test
    public void testStdioClientSayHello() throws Exception {
        Client c = new Client();
        c.sayHello();
    }

    @Test
    public void testClientGetNewApiClient() throws Exception {
        Client c = new Client();
        org.junit.Assert.assertTrue(c.getNewApiClient() instanceof ApiClient);
    }

    @Test
    public void testClientGetApiClient() throws Exception {
        Client c = new Client();
        org.junit.Assert.assertTrue(c.getApiClient() instanceof ApiClient);

    }
    @Test
    public void testClientGetNewApiClientAssigns() throws Exception {
        Client c = new Client();
        org.junit.Assert.assertNotNull(c.apiClient);
    }
    @Test
    public void testClientGetNewApiClientAssignsIsApiClient() throws Exception {
        Client c = new Client();
        org.junit.Assert.assertTrue(c.apiClient instanceof ApiClient);
    }
    @Test
    public void testClientGetNewApiClientAssignsExists() throws Exception {
        Client c = new Client();
        org.junit.Assert.assertNull(c.apiClient.exists);
    }

    @Test
    public void testGetClientContext() throws Exception {
        Client c = new Client();
        org.junit.Assert.assertEquals(c.getContext(),null);

    }

    @Test
    public void testClientHasTerm() throws Exception {
        Client c = new Client();
        //Won't have a console while running the tests.
        org.junit.Assert.assertNull(c.term);
    }

    @Test
    public void testClientWaitForInput() throws Exception {
        Client c = new Client();
        c.waitForInput();

    }
}

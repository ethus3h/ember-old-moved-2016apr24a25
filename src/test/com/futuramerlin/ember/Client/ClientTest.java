package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.ApiClient.ApiClient;
import com.futuramerlin.ember.Client.Client;
//import org.junit.Test;
import org.junit.Assert;

/**
 * Created by elliot on 14.10.29.
 */
public class ClientTest {
 /*   @Test
    public void testCreateStdioClient() throws Exception {
        Client c = new Client();

    }

    @Test
    public void testStdioClientSayHello() throws Exception {
        Client c = new Client();
        c.sayHello();
    }

/*    @Test
    public void testClientGetNewApiClient() throws Exception {
        Client c = new Client();
        assert(c.getNewApiClient() instanceof ApiClient);
    }

    @Test
    public void testClientGetApiClient() throws Exception {
        Client c = new Client();
        assert(c.getApiClient() instanceof ApiClient);

    }
    @Test */
    public void testClientGetNewApiClientAssigns() throws Exception {
        Client c = new Client();
        assert(c.apiClient.exists);
/*         org.junit.Assert.assertNotNull(c.apiClient);
        org.junit.Assert.assertTrue(c.apiClient instanceof ApiClient);
        org.junit.Assert.assertNull(c.apiClient.exists);
        org.junit.Assert.assertNotNull(c.apiClient.exists);
        org.junit.Assert.assertTrue(c.apiClient.exists);
        org.junit.Assert.assertFalse(c.apiClient.exists); */
    }
}

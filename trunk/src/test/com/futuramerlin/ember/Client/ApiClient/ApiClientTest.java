package com.futuramerlin.ember.Client.ApiClient;

import org.junit.Test;

/**
 * Created by elliot on 14.11.01.
 */
public class ApiClientTest {
    @Test
    public void testCreateApiClient() throws Exception {
        ApiClient c = new ApiClient();
        assert(c instanceof ApiClient);

    }
}

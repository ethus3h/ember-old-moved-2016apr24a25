package com.futuramerlin.ember.Client.WebClient;

import org.junit.Test;

/**
 * Created by elliot on 14.10.29.
 */
public class WebClientTest {
    @Test
    public void testCreateWebClient() throws Exception {
        WebClient c = new WebClient();
        c.sayHello();
    }
}

package com.futuramerlin.ember.Client.StdioClient;

import org.junit.Test;

/**
 * Created by elliot on 14.10.29.
 */
public class StdioClientTest {
    @Test
    public void testCreateStdioClient() throws Exception {
        StdioClient c = new StdioClient();

    }

    @Test
    public void testStdioClientSayHello() throws Exception {
        StdioClient c = new StdioClient();
        c.sayHello();
    }
}

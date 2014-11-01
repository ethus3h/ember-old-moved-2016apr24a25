package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.Client;
import org.junit.Test;

/**
 * Created by elliot on 14.10.29.
 */
public class ClientTest {
    @Test
    public void testCreateStdioClient() throws Exception {
        Client c = new Client();

    }

    @Test
    public void testStdioClientSayHello() throws Exception {
        Client c = new Client();
        c.sayHello();
    }
}

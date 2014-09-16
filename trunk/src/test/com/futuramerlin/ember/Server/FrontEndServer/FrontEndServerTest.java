package com.futuramerlin.ember.Server.FrontEndServer;

import org.junit.Test;

public class FrontEndServerTest {
    public FrontEndServerTest() {
    }

    @Test
    public void testCreateFrontEndServer() throws Exception {
        FrontEndServer s = new FrontEndServer();

    }

    @Test
    public void testStoreInFrontEndServer() throws Exception {
        FrontEndServer s = new FrontEndServer();
        s.store("Hello, World!".getBytes("UTF-8"));

    }
}
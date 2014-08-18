package com.futuramerlin.ember.Server;

import org.junit.Test;

import static org.junit.Assert.assertEquals;

public class ServerTest {
    @Test
    public void testCreateServer() throws Exception {
        Server s = new Server();
    }

    @Test
    public void testServerEcho() throws Exception {
        Server s = new Server();
        assertEquals("Hello, World!", s.echo("Hello, World!"));
    }


    @Test
    public void testServerPrint() throws Exception {
        Server s = new Server();
        s.print("Hello, World!");
    }

    @Test
    public void testCreateHttpServerInstance() throws Exception {
        Server s = new Server();
        s.createHttpServerInstance();

    }
    @Test
    public void testCreateHttpLoopbackServerInstance() throws Exception {
        Server s = new Server();
        s.createHttpLoopbackServerInstance();

    }
}
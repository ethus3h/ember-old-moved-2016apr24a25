package com.futuramerlin.ember.Server;

import com.sun.net.httpserver.HttpServer;

import java.io.IOException;
import java.net.InetAddress;
import java.net.InetSocketAddress;

/**
 * Created by PermissionGiver on 8/16/14.
 */
public class Server {
    public String echo(String s) {
        return s;
    }

    public void print(String s) {
        System.out.println(s);
    }

    public void createHttpServerInstance() throws IOException {
        HttpServer server = HttpServer.create(new InetSocketAddress(80),0);
    }

    public void createHttpLoopbackServerInstance() throws IOException {

        HttpServer server = HttpServer.create(new InetSocketAddress(InetAddress.getLoopbackAddress(), 80),0);
    }
}

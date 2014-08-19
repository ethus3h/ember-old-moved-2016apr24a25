package com.futuramerlin.ember.Server;

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

    public void createHttpServerInstance() throws Exception {
       JettyServer j = new JettyServer();
       j.start();
    }
}

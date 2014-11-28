package com.futuramerlin.ember.Client.Session;

import com.futuramerlin.ember.Client.Context;

/**
 * Created by elliot on 14.11.28.
 */
public class NullSession extends Session {
    public NullSession() {
        super();
        this.context = new Context("null");
    }
}

package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Common.Exception.NoContextsFoundException;
import org.junit.Test;

/**
 * Created by elliot on 14.11.27.
 */
public class SessionCreatorTest {
    @Test
    public void testNewSessionCreator() throws Exception {
        SessionCreator a = new SessionCreator();
        org.junit.Assert.assertNotNull(a.sessions);


    }

    @Test(expected=NoContextsFoundException.class)
    public void testGetContexts() throws Exception {
        SessionCreator a = new SessionCreator();
        a.getContexts();
    }
}

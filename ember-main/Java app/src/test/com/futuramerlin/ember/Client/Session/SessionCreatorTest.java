package com.futuramerlin.ember.Client.Session;

import com.futuramerlin.ember.Client.Session.SessionCreator;
import com.futuramerlin.ember.Common.Exception.NoContextsFoundException;
import com.futuramerlin.ember.Common.Exception.NullContextOnlyException;
import org.junit.Rule;
import org.junit.Test;
import org.junit.contrib.java.lang.system.StandardOutputStreamLog;

/**
 * Created by elliot on 14.11.27.
 */
public class SessionCreatorTest {
    @Test
    public void testNewSessionCreator() throws Exception {
        SessionCreator a = new SessionCreator();
        org.junit.Assert.assertNotNull(a.sessions);


    }

    @Rule
    public final StandardOutputStreamLog log = new StandardOutputStreamLog();
    @Test
    public void testGetContexts() throws Exception {
        SessionCreator a = new SessionCreator();
        a.getContexts();
        org.junit.Assert.assertEquals("It doesn't look like you're using Ember in a context in which you can give it commands. Presumably in a later version, a scriptable interface will be available.\n",log.getLog());
    }
}

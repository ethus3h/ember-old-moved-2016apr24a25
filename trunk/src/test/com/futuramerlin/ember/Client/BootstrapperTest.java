package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.ApiClient.ApiClient;
import com.futuramerlin.ember.Common.Exception.ApiClientAlreadyExistsException;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import org.junit.After;
import org.junit.Before;
import org.junit.Rule;
import org.junit.Test;
import org.junit.contrib.java.lang.system.StandardErrorStreamLog;
import org.junit.contrib.java.lang.system.StandardOutputStreamLog;

import java.io.ByteArrayOutputStream;
import java.io.PrintStream;

/**
 * Created by elliot on 14.11.01.
 */
public class BootstrapperTest {
    public static void main(String[] args) throws Exception {
        BootstrapperTest c = new BootstrapperTest();
        c.testBootstrapperGetNewApiClientAssigns();
    }

    @Test
    public void testBootstrapperRun() throws Exception {
        Bootstrapper b = new Bootstrapper();

    }

    @Test(expected=ApiClientAlreadyExistsException.class)
    public void testBootstrapperGetNewApiClient() throws Exception {
        Bootstrapper c = new Bootstrapper();
        org.junit.Assert.assertTrue(c.getNewApiClient() instanceof ApiClient);
    }

    @Test
    public void testBootstrapperGetApiClient() throws Exception {
        Bootstrapper c = new Bootstrapper();
        org.junit.Assert.assertTrue(c.getApiClient() instanceof ApiClient);

    }
    @Test
    public void testBootstrapperGetNewApiClientAssigns() throws Exception {
        Bootstrapper c = new Bootstrapper();
        org.junit.Assert.assertNotNull(c.apiClient);
    }
    @Test
    public void testBootstrapperGetNewApiClientAssignsIsApiClient() throws Exception {
        Bootstrapper c = new Bootstrapper();
        org.junit.Assert.assertTrue(c.apiClient instanceof ApiClient);
    }
    @Test
    public void testBootstrapperGetNewApiClientAssignsExists() throws Exception {
        Bootstrapper c = new Bootstrapper();
        org.junit.Assert.assertNull(c.apiClient.exists);
    }

    @Test
    public void testGetBootstrapperContext() throws Exception {
        Bootstrapper c = new Bootstrapper();
        org.junit.Assert.assertEquals(c.getContext(), null);

    }

/*
    @Test
    public void testBootstrapperOperate() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Bootstrapper c = new Bootstrapper();
        c.operate();

    }*/

    @Test
    public void testBootstrapperNullContextMessage() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.printNullContextMessage();


    }


    @Test
    public void testStart() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.start();


    }
    @Test
    public void testStop() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.stop();


    }

    @Test
    public void testMessage() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.message("");

    }

    @Test
    public void testProcessSignalHandler() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.processSignalHandler(1);

    }

    @Test
    public void testRun() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.run();

    }
    //help from http://stackoverflow.com/questions/1119385/junit-test-for-system-out-println
/*    private final ByteArrayOutputStream outContent = new ByteArrayOutputStream();
    private final ByteArrayOutputStream errContent = new ByteArrayOutputStream();

    @Before
    public void setUpStreams() {
        System.setOut(new PrintStream(outContent));
        System.setErr(new PrintStream(errContent));
    }

    @After
    public void cleanUpStreams() {
        System.setOut(null);
        System.setErr(null);
    } */
    @Rule
    public final StandardOutputStreamLog log = new StandardOutputStreamLog();
    @Test(expected=ApiClientAlreadyExistsException.class)
    public void testCatchApiClientAlreadyExists() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.getNewApiClient();
        c.run();
        org.junit.Assert.assertEquals("Failed to create: ApiClient already exists.",log.getLog());
    }
  //  @Test
    /**
     * Tests Bootstrapper's operate() method. operate() will print a message indicating that it has
     * no way to receive command input.
     */
/*
    public void testOperateNoTerminal() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.operate();
        org.junit.Assert.assertEquals("It doesn't look like you're using Ember in a context in which you can give it commands. Presumably in a later version, a scriptable interface will be available.\n",log.getLog());
    }
*/

    @Test
    public void testPause() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.pause();

    }
    @Test
    public void testResume() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.resume();

    }
    @Test
    public void testTerminate() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.terminate();

    }
    @Test
    public void testKill() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.kill();

    }
/*

    @Test
    public void testOperateTerminal() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.running = true;
        c.context = "terminal";
        c.operate();
        org.junit.Assert.assertEquals("No terminal was found. Please only use TerminalInterfaceOperator when a terminal is available.\n",log.getLog());
        org.junit.Assert.assertFalse(c.running);
    }
*/

    @Test
    public void testListInteractionContexts() throws Exception {
        Bootstrapper c = new Bootstrapper();
        org.junit.Assert.assertArrayEquals(c.listInteractionContexts(),new String[]{});

    }
}

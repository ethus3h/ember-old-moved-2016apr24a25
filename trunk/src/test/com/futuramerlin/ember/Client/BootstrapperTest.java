package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.ApiClient.ApiClient;
import com.futuramerlin.ember.Common.Exception.ApiClientAlreadyExistsException;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import org.junit.After;
import org.junit.Before;
import org.junit.Test;

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

    @Test
    public void testBootstrapperHasTerm() throws Exception {
        Bootstrapper c = new Bootstrapper();
        //Won't have a console while running the tests.
        org.junit.Assert.assertNull(c.term);
    }

    @Test(expected=NoTerminalFoundException.class)
    public void testBootstrapperWaitForInput() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        //Won't have a console while running the tests.
        Bootstrapper c = new Bootstrapper();
        c.waitForInput();
    }

    @Test
    public void testBootstrapperOperate() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Bootstrapper c = new Bootstrapper();
        c.operate();

    }

    @Test
    public void testBootstrapperNullContextMessage() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.printNullContextMessage();


    }

    @Test
    public void testInteractOnTerminal() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Bootstrapper c = new Bootstrapper();
        c.interactOnTerminal();


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
    public void testCommand() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.command("");

    }
    @Test(expected=NoTerminalFoundException.class)
    public void testProcessInput() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Bootstrapper c = new Bootstrapper();
        c.processInput();

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
        org.junit.Assert.assertEquals(c.term,System.console());

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
    @Test
    public void testCatchApiClientAlreadyExists() throws Exception {
        Bootstrapper c = new Bootstrapper();
        c.apiClient = c.getApiClient();
        final ByteArrayOutputStream outContent = new ByteArrayOutputStream();
        final ByteArrayOutputStream errContent = new ByteArrayOutputStream();
        System.setOut(new PrintStream(outContent));
        c.run();
        org.junit.Assert.assertEquals("Failed to create: ApiClient already exists.",outContent.toString());


    }
}

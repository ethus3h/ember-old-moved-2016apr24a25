package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.ApiClient.ApiClient;
import com.futuramerlin.ember.Client.Bootstrapper;
import com.futuramerlin.ember.Common.Exception.ApiClientAlreadyExistsException;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import org.junit.Test;

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
}

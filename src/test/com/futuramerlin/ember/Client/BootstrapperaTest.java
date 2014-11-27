package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.ApiClient.ApiClient;
import com.futuramerlin.ember.Common.Exception.ApiClientAlreadyExistsException;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import org.junit.Test;

/**
 * Created by elliot on 14.11.01.
 */
public class BootstrapperaTest {
    public static void main(String[] args) throws Exception {
        BootstrapperaTest c = new BootstrapperaTest();
        c.testBootstrapperGetNewApiClientAssigns();
    }
    @Test
    public void testBootstrapperRun() throws Exception {
        Bootstrappera b = new Bootstrappera();

    }

    @Test(expected=ApiClientAlreadyExistsException.class)
    public void testBootstrapperGetNewApiClient() throws Exception {
        Bootstrappera c = new Bootstrappera();
        org.junit.Assert.assertTrue(c.getNewApiClient() instanceof ApiClient);
    }

    @Test
    public void testBootstrapperGetApiClient() throws Exception {
        Bootstrappera c = new Bootstrappera();
        org.junit.Assert.assertTrue(c.getApiClient() instanceof ApiClient);

    }
    @Test
    public void testBootstrapperGetNewApiClientAssigns() throws Exception {
        Bootstrappera c = new Bootstrappera();
        org.junit.Assert.assertNotNull(c.apiClient);
    }
    @Test
    public void testBootstrapperGetNewApiClientAssignsIsApiClient() throws Exception {
        Bootstrappera c = new Bootstrappera();
        org.junit.Assert.assertTrue(c.apiClient instanceof ApiClient);
    }
    @Test
    public void testBootstrapperGetNewApiClientAssignsExists() throws Exception {
        Bootstrappera c = new Bootstrappera();
        org.junit.Assert.assertNull(c.apiClient.exists);
    }

    @Test
    public void testGetBootstrapperContext() throws Exception {
        Bootstrappera c = new Bootstrappera();
        org.junit.Assert.assertEquals(c.getContext(), null);

    }

    @Test
    public void testBootstrapperHasTerm() throws Exception {
        Bootstrappera c = new Bootstrappera();
        //Won't have a console while running the tests.
        org.junit.Assert.assertNull(c.term);
    }

    @Test(expected=NoTerminalFoundException.class)
    public void testBootstrapperWaitForInput() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        //Won't have a console while running the tests.
        Bootstrappera c = new Bootstrappera();
        c.waitForInput();
    }

    @Test
    public void testBootstrapperOperate() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Bootstrappera c = new Bootstrappera();
        c.operate();

    }

    @Test
    public void testBootstrapperNullContextMessage() throws Exception {
        Bootstrappera c = new Bootstrappera();
        c.printNullContextMessage();


    }

    @Test
    public void testInteractOnTerminal() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Bootstrappera c = new Bootstrappera();
        c.interactOnTerminal();


    }

    @Test
    public void testStart() throws Exception {
        Bootstrappera c = new Bootstrappera();
        c.start();


    }
    @Test
    public void testStop() throws Exception {
        Bootstrappera c = new Bootstrappera();
        c.stop();


    }

    @Test
    public void testCommand() throws Exception {
        Bootstrappera c = new Bootstrappera();
        c.command("");

    }
    @Test(expected=NoTerminalFoundException.class)
    public void testProcessInput() throws Exception, ZeroLengthInputException, NoTerminalFoundException {
        Bootstrappera c = new Bootstrappera();
        c.processInput();

    }
    @Test
    public void testMessage() throws Exception {
        Bootstrappera c = new Bootstrappera();
        c.message("");

    }
}

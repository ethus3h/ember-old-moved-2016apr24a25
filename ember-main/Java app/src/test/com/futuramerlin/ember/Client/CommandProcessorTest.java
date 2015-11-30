package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.Session.Session;
import org.junit.Rule;
import org.junit.Test;
import org.junit.contrib.java.lang.system.StandardOutputStreamLog;

/**
 * Created by elliot on 14.11.27.
 */
public class CommandProcessorTest {

    @Test
    public void testCreateCommandProcessor() throws Exception {
        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);

    }

    @Test
    public void testCommandRq() throws Exception {
        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);
        p.command("");

    }

    @Rule
    public final StandardOutputStreamLog log = new StandardOutputStreamLog();
    /*@Test
    public void testHelp() throws Exception {

        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);
        p.command("help");
        System.out.println("DOOOOOOOM1:!" + log.getLog() + "DOOOMendLOg.");
        System.out.println("DOOOOOOOM2:!"+"The / $ at the beginning of some lines is the \"prompt\". When you " +
                "see the prompt, you can type a command. Type the return key to run a command. To see a list of available " +
                "commands, run the command: \"ls /bin\". To change directories, use the \"cd\" command."+"DOOOMendString.");
        org.junit.Assert.assertTrue(log.getLog().contains("The / $ at the beginning of some lines is the \"prompt\". When you " +
                "see the prompt, you can type a command. Type the return key to run a command. To see a list of available " +
                "commands, run the command: \"ls /bin\". To change directories, use the \"cd\" command."));
    }*/
    @Test
    public void testQuit() throws Exception {

        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);
        p.command("quit");
    }

    @Test
    public void testUnknownCommand() throws Exception {
        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);
        p.command("thisShouldNeverBeARealCommand");
        org.junit.Assert.assertEquals("That is not a known command. To see a list of available commands, run the command: \"l /bin\".\n",log.getLog());


    }
    @Test
    public void testEmptyCommand() throws Exception {
        Session s = new Session();
        CommandProcessor p = new CommandProcessor(s);
        p.command("");
        org.junit.Assert.assertFalse(log.getLog().contains("That is not a known command. To see a list of available commands, run the command: \"l /bin\".\n"));


    }
}

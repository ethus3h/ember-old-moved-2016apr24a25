package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.Session.Session;
import com.futuramerlin.ember.Common.Exception.CommandExecutionError;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;

/**
 * Created by elliot on 14.11.27.
 */
public class CommandProcessor {
    private Session session;

    public CommandProcessor(Session s) {
        this.session = s;
    }

    public void command(String c) throws CommandExecutionError {
        String cmd = "";
        if(c.length() != 0) {
            cmd = "DisplayUnknownCommandMessage";
        }
        if (c.toLowerCase().equals("quit")) {
            this.session.running = false;
            cmd = "Quit";
        }
        if(c.toLowerCase().equals("help")) {
            cmd = "Help";
            System.out.println("The / $ at the beginning of some lines is the \"prompt\". When you" +
                    " see the prompt, you can type a command. Type the return key to run a command. To see a list of available " +
                    "commands, run the command: \"l /bin\". To change directories, use the \"cd\" command.\n");
        }
        else {
            System.out.println("That is not a known command. To see a list of available commands, run the command: \"l /bin\".");
        }
        try {
            this.session.pm.start("Client.Commands." + cmd, this.session, c);
        }
        catch (Exception e) {
            e.printStackTrace();
            //throw new CommandExecutionError(e);
        }
    }
}

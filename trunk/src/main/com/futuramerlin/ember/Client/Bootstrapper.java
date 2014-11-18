package com.futuramerlin.ember.Client;

import com.futuramerlin.ember.Client.ApiClient.ApiClient;
import com.futuramerlin.ember.Common.DataProcessor.StringProcessor;
import com.futuramerlin.ember.Common.Exception.ApiClientAlreadyExistsException;
import com.futuramerlin.ember.Common.Exception.NoTerminalFoundException;
import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import com.futuramerlin.ember.Common.Process.EmberProcess;

import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.Console;

/**
 * Created by elliot on 14.11.01.
 */
public class Bootstrapper implements EmberProcess {
    public ApiClient apiClient;
    public Console term;
    private String context;
    private boolean running;
    private RecordManager rm;
    private JFrame frame;
    private Button button;
    private Label commandsLabel;
    private TextField input;

    @Override
    public void processSignalHandler(Integer signal) {

    }

    @Override
    public void run() {
        this.term = System.console();
        try {
            this.start();
        }
        catch(ApiClientAlreadyExistsException e) {
            System.out.println("Failed to create: ApiClient already exists.");
        }
        try {
            this.operate();
        }
        catch(Exception e) {
            System.out.println("Something went wrong. Ember will now shut down.");
        }
        this.stop();
    }

    @Override
    public void pause() {

    }

    @Override
    public void resume() {

    }

    @Override
    public void terminate() {

    }

    @Override
    public void kill() {

    }

    public Bootstrapper() throws ApiClientAlreadyExistsException {
        this.term = System.console();
        this.apiClient = this.getApiClient();
    }
    public void start() throws ApiClientAlreadyExistsException {
        this.apiClient = this.getApiClient();
        this.getContext();
        this.running = true;
        this.message("Ember has started, and is ready to use.");
    }

    public void stop() {
        this.message("Ember is stopping now. Please wait; it may take a little while...");
        this.message("Ember has stopped.");
    }

    public ApiClient getNewApiClient() throws ApiClientAlreadyExistsException {
        if(this.apiClient==null) {
            this.apiClient = new ApiClient();
        }
        else {
            throw new ApiClientAlreadyExistsException();
        }
        return this.apiClient;
    }

    public ApiClient getApiClient() throws ApiClientAlreadyExistsException {
        if(this.apiClient == null) {
            this.getNewApiClient();
        }
        return this.apiClient;
    }

    public String waitForInput() throws ZeroLengthInputException, NoTerminalFoundException {
        if(this.term != null) {
            StringProcessor p = new StringProcessor();
            return this.term.readLine("$ ");
        }
        throw new NoTerminalFoundException();
    }

    public String getContext() {
        if(this.term != null) {
            this.context = "terminal";
        }
        else {
            this.context = null;
        }
        return context;
    }

    public void operate() throws ZeroLengthInputException, NoTerminalFoundException {
        if(this.context == null) {
            this.printNullContextMessage();
        }
        if(this.context == "terminal") {
            this.interactOnTerminal();
        }
    }

    public void interactOnTerminal() throws ZeroLengthInputException, NoTerminalFoundException {
        while (this.running) {
            //help from https://docs.oracle.com/javase/tutorial/uiswing/components/frame.html and http://stackoverflow.com/questions/11927963/get-input-from-jframe
            this.frame = new JFrame("RecordManager");
            this.button = new Button();
            this.commandsLabel = new Label("RecordManager");
            this.input = new TextField("");
            this.frame.getContentPane().add(commandsLabel, BorderLayout.CENTER);
            this.frame.getContentPane().add(input, BorderLayout.CENTER);
            this.frame.getContentPane().add(button, BorderLayout.CENTER);
            this.frame.pack();
            this.frame.setVisible(true);
            this.processInput();
        }
    }

    public void printNullContextMessage() {
        System.out.println("It doesn't look like you're using Ember in a context in which you can give it commands. Presumably in a later version, a scriptable interface will be available.");
    }


    public void command(String c) {

        //System.out.println(c);
        //System.out.println(this.running);
        //System.out.println(c.equals("quit"));
        if (c.equals("quit")) {
            //System.out.println("QUITTING");
            this.running = false;
        }
        if(c.matches("c \\d+")) {
            this.rm = new RecordManager();
        }
        if(c.matches("s \\d+ \\d+")) {
            this.rm.add(Integer.parseInt(c.split(" ")[0]),Integer.parseInt(c.split(" ")[1]));
        }
        if(c.matches("e \\d+")) {

        }
        if(c.matches("r \\d+")) {
            System.out.println(this.rm.records.get((Integer.parseInt(c.split(" ")[0]))));
        }
        if(c.matches("d \\d+")) {

        }
        if(c.equals("xs")) {

        }
        if(c.equals("xh")) {

        }
        if(c.equals("xa")) {

        }
        if(c.equals("xb")) {

        }
        if(c.equals("xp")) {

        }
    }

    public void processInput() throws ZeroLengthInputException, NoTerminalFoundException {
        String c = this.waitForInput();
        this.button.addActionListener(new ActionListener() {
            public String input;
            public RecordManager rm;

            public void actionPerformed(ActionEvent arg0) {
                this.command(this.input);
            }
            public void command(String input) {

                //System.out.println(c);
                //System.out.println(this.running);
                //System.out.println(c.equals("quit"));
                if(c.matches("c \\d+")) {
                    this.rm = new RecordManager();
                }
                if(c.matches("s \\d+ \\d+")) {
                    this.rm.add(Integer.parseInt(c.split(" ")[0]),Integer.parseInt(c.split(" ")[1]));
                }
                if(c.matches("e \\d+")) {

                }
                if(c.matches("r \\d+")) {
                    System.out.println(this.rm.records.get((Integer.parseInt(c.split(" ")[0]))));
                }
                if(c.matches("d \\d+")) {

                }
                if(c.equals("xs")) {

                }
                if(c.equals("xh")) {

                }
                if(c.equals("xa")) {

                }
                if(c.equals("xb")) {

                }
                if(c.equals("xp")) {

                }
            }
        });
        this.command(c);
    }

    public void message(String s) {
        if((this.context == null) || (this.context == "terminal")) {
            System.out.println(s);
        }
    }
}

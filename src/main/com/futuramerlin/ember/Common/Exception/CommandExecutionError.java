package com.futuramerlin.ember.Common.Exception;

/**
 * Created by elliot on 14.11.28.
 */
public class CommandExecutionError extends Exception {
    public final Exception exception;

    public CommandExecutionError(Exception e) {
        this.exception = e;
    }
}

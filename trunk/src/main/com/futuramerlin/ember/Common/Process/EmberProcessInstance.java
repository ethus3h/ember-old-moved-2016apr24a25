package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Common.Exception.*;

import java.lang.reflect.InvocationTargetException;
import java.lang.Thread;


/**
 * Created by elliot on 14.11.01.
 */
public class EmberProcessInstance implements Runnable {
    private String[] args;
    private String target;
    public Integer pid;
    public Exception e;
    private Class<?> c;
    private Object o;

    public EmberProcessInstance(ProcessManager e, String c, String[] args) throws Exception {
        this.target = c;
        this.args = args;
        this.start();
    }

    public EmberProcessInstance(ProcessManager e, String c) throws Exception {
        new EmberProcessInstance(e, c, null);
    }

    public void start() throws Exception, classRunMethodMissingException {
        (new Thread(this)).start();
        if(this.e != null) {
            throw this.e;
        }
    }
    @Override
    public void run() {
        try {
            this.execute(Class.forName("com.futuramerlin.ember."+this.target));
        } catch (Exception e) {
            this.e=e;
        }
    }

    private void execute(Class<?> c, Object... args) throws IllegalAccessException, InstantiationException, classNotRunnableException, classRunMethodMissingException, classIllegalAccessException, classInvocationTargetException {
        this.c = c;
        Class[] argTypes = new Class[args.length];
        Integer i = 0;
        for (Object o : args) {
            argTypes[i] = o.getClass();
            i++;
        }
        try {
            c.getDeclaredMethod("run", argTypes).invoke(this.c.newInstance(), args);
        } catch (NoSuchMethodException x) {
            throw new classRunMethodMissingException();
        } catch (IllegalAccessException x) {
            throw new classIllegalAccessException();
        } catch (InvocationTargetException x) {
            throw new classInvocationTargetException();
        }
    }
}

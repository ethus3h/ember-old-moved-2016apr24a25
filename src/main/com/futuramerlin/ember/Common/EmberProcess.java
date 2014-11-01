package com.futuramerlin.ember.Common;

import com.futuramerlin.ember.Common.Exception.*;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.Arrays;
import java.lang.Thread;


/**
 * Created by elliot on 14.11.01.
 */
public class EmberProcess implements Runnable {
    private String[] args;
    private String target;
    public Integer pid;
    public Exception e;
    public EmberProcess(ProcessManager e, String c, String[] args) {
        this.target = target;
        this.args = args;
    }

    public EmberProcess(ProcessManager e, String c) {
        new EmberProcess(e, c, null);
    }

    public void start() throws Exception, classNotRunnableException {
        (new Thread(this)).start();
        if(this.e != null) {
            throw this.e;
        }
    }
    @Override
    public void run() {
        try {
            this.execute(Class.forName(this.target));
        } catch (Exception e) {
            this.e=e;
        }
    }

    private void execute(Class<?> c, String... args) throws classNotRunnableException, classNotExistsException, classIllegalAccessException, classInvocationTargetException, classLacksSignalInterfaceException {
        //based on http://docs.oracle.com/javase/tutorial/reflect/member/methodInvocation.html
        Class[] argTypes = new Class[] { String[].class };
        try {
            Method handler = c.getDeclaredMethod("processSignalHandler", argTypes);
        } catch (NoSuchMethodException x) {
            throw new classLacksSignalInterfaceException();
        }
        try {
            Method main = c.getDeclaredMethod("create", argTypes);
            String[] mainArgs = Arrays.copyOfRange(args, 1, args.length);
            System.out.format("invoking %s.main()%n", c.getName());
            main.invoke(null, (Object)mainArgs);
        } catch (NoSuchMethodException x) {
            throw new classNotRunnableException();
        } catch (IllegalAccessException x) {
            throw new classIllegalAccessException();
        } catch (InvocationTargetException x) {
            throw new classInvocationTargetException();
        }
    }
}

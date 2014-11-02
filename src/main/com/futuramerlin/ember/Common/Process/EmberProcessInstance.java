package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Common.Exception.*;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.Arrays;
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

    public void start() throws Exception, classNotRunnableException {
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

    private void execute(Class<?> c, Object... args) throws classNotRunnableException, classNotExistsException, classIllegalAccessException, classInvocationTargetException, classLacksSignalInterfaceException, IllegalAccessException, InstantiationException {
        //based on http://docs.oracle.com/javase/tutorial/reflect/member/methodInvocation.html
        this.c = c;
        this.o = this.c.newInstance();
        Class[] argTypes = new Class[args.length];
        Integer i = 0;
        for (Object o : args) {
            argTypes[i] = o.getClass();
            i++;
        }
        try {
            //help from http://stackoverflow.com/questions/15268767/getmethods-returns-method-i-havent-defined-when-implementing-a-generic-interf
            for (Method method : c.getDeclaredMethods()) {
                System.out.println(method.getName() + "\t");
            }
            //from http://www.tutorialspoint.com/java/lang/class_getdeclaredmethod.htm
            Class[] cArg = new Class[1];
            cArg[0] = Integer.class;
            Method handler = c.getDeclaredMethod("processSignalHandler", cArg);
        } catch (NoSuchMethodException x) {
            throw new classLacksSignalInterfaceException();
        }
        try {
            Method main = c.getDeclaredMethod("run", argTypes);
            //String[] mainArgs = Arrays.copyOfRange(args, 1, args.length);
            System.out.format("invoking %s.main()%n", c.getName());
            main.invoke(this.o, args);
        } catch (NoSuchMethodException x) {
            throw new classNotRunnableException();
        } catch (IllegalAccessException x) {
            throw new classIllegalAccessException();
        } catch (InvocationTargetException x) {
            throw new classInvocationTargetException();
        }
    }
}

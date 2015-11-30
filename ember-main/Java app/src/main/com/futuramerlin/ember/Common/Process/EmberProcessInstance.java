package com.futuramerlin.ember.Common.Process;

import com.futuramerlin.ember.Client.Session.Session;
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
    public Session session;
    public Exception e;
    public ProcessManager pm;
    private Class<?> c;
    private Object o;

    public EmberProcessInstance(ProcessManager e, String c, String[] args) throws Exception {
        new EmberProcessInstance((Session)null,c,args);
    }

    public EmberProcessInstance(ProcessManager e, String c) throws Exception {
        new EmberProcessInstance(e, c, null);
    }

    public EmberProcessInstance(Session s, String c, String[] args) throws Exception {
        this.session = s;
        this.target = c;
        this.args = args;
        if(this.session == null) {
            this.pm = null;
        }
        else {
            this.pm = s.pm;

        }
        this.start();
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
            this.execute(Class.forName("com.futuramerlin.ember."+this.target),this.target,this.session,this.args);
        } catch (Exception e) {
            this.e=e;
        }
    }
    public void passArgumentsAndBegin(Session s, ProcessManager pm, String[] args) {

    }

    private void execute(Class<?> c, String target, Session s, Object... args) throws IllegalAccessException, InstantiationException, classNotRunnableException, classRunMethodMissingException, classIllegalAccessException, classInvocationTargetException {
        this.c = c;
        Class[] argTypes = new Class[args.length];
        Integer i = 0;
        if(args != null) {
            //Need to make sure that argTypes matches the arguments to pass to start
            argTypes[0] = Session.class;
            for (Object o : args) {
                if(o != null) {
                    argTypes[i] = o.getClass();
                }
                else {
                    argTypes[i] = null;
                }
                i++;
            }
        }
        try {
            c.getDeclaredMethod("start", argTypes).invoke(this.c.newInstance(), this.session, args);
        } catch (NoSuchMethodException x) {
            throw new classRunMethodMissingException();
        } catch (IllegalAccessException x) {
            throw new classIllegalAccessException();
        } catch (InvocationTargetException x) {
            throw new classInvocationTargetException();
        }
    }
}

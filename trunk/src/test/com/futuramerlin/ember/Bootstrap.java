package com.futuramerlin.ember;

import com.futuramerlin.ember.DataType.HW2Tree.TestMyTree;
import com.futuramerlin.ember.DataType.HW2Tree.TreeNodeTest;
import com.futuramerlin.ember.DataType.HW2Tree.TreeTest;

import java.lang.reflect.InvocationTargetException;

/**
 * Created by elliot on 7 October 14.
 */
public class Bootstrap {
    public static void main(String[] args) throws InvocationTargetException, IllegalAccessException {
        TestMyTree a = new TestMyTree();
        TreeNodeTest b = new TreeNodeTest();
        b.callAll();
        TreeTest c = new TreeTest();
        c.callAll();
    }

}

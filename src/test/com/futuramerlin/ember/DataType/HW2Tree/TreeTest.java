package com.futuramerlin.ember.DataType.HW2Tree;

import org.junit.Test;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;

import static org.junit.Assert.assertEquals;

/**
 * Created by elliot on 6 October 14.
 */
public class TreeTest {
    public void callAll() throws
            IllegalArgumentException, IllegalAccessException, InvocationTargetException {
        Method[] methods = this.getClass().getDeclaredMethods();
        for (Method m : methods) {
            m.invoke(null,null);
        }
    }
    @Test
    public void testNewTree() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
        Tree t = new MyTree(n);

    }

    @Test
    public void testIsEmpty() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
        Tree t = new MyTree(n);
        assertEquals(true,t.isEmpty());

    }

    @Test
    public void testIsEmptyFalse() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
        TreeNode<String> n2 = new MyTreeNode("B");
        Tree t = new MyTree(n);
        n.addChild(n2);
        assertEquals(false,t.isEmpty());

    }

    @Test
    public void testGetRoot() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
        Tree t = new MyTree(n);
        assertEquals(n, t.getRoot());

    }

    @Test
    public void testSize() throws Exception {

        TreeNode<String> n = new MyTreeNode("A");
        Tree t = new MyTree(n);
        assertEquals(1,t.size());

    }

    @Test
    public void testSizeMoreNodes() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        TreeNode<String> n4 = new MyTreeNode("DOO00OOM!");
        TreeNode<String> n5 = new MyTreeNode("DOO01OOM!");
        TreeNode<String> n6 = new MyTreeNode("DOO02OOM!");
        TreeNode<String> n7 = new MyTreeNode("DOO03OOM!");
        n.addChild(n2);
        n.addChild(n3);
        n3.addChild(n4);
        n4.addChild(n5);
        n4.addChild(n6);
        n4.addChild(n7);
        Tree t = new MyTree(n);
        assertEquals(7, t.size());

    }

    @Test
    public void testGetPreOrder() throws Exception {


    }

    @Test
    public void testGetPostOrder() throws Exception {


    }
}

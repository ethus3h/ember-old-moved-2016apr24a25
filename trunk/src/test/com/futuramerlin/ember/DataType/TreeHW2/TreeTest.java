package com.futuramerlin.ember.DataType.TreeHW2;

import org.junit.Test;

import java.util.ArrayList;
import java.util.List;

import static org.junit.Assert.assertEquals;

/**
 * Created by elliot on 6 October 14.
 */
public class TreeTest {
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
}

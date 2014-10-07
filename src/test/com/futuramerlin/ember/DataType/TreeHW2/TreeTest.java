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
    public void testNewTreeNode() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
    }
    @Test
    public void testNewTree() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
        Tree t = new MyTree(n);

    }

    @Test
    public void testCountRoot() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
        Tree t = new MyTree(n);
        assertEquals(t.size(), 1);

    }

    @Test
    public void testAddChild() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<Integer> n2 = new MyTreeNode(50);
        n.addChild(n);

    }
    @Test
    public void testGetChildren() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<Integer> n2 = new MyTreeNode(50);
        List<TreeNode> testComparison = new ArrayList<TreeNode>();
        testComparison.add(0,n2);
        assertEquals(n.getChildren(), testComparison);

    }
}

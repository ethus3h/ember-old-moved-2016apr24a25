package com.futuramerlin.ember.DataType.TreeHW2;

import org.junit.Test;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.List;

import static org.junit.Assert.assertEquals;

/**
 * Created by elliot on 7 October 14.
 */
public class TreeNodeTest {
    public void callAll() throws
            IllegalArgumentException, IllegalAccessException, InvocationTargetException {
        Method[] methods = this.getClass().getDeclaredMethods();
        for (Method m : methods) {
            m.invoke(null,null);
        }
    }
    @Test
    public void testNewTreeNode() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
    }
    @Test
    public void testCountRoot() throws Exception {
        TreeNode<String> n = new MyTreeNode("A");
        Tree t = new MyTree(n);
        assertEquals(1, t.size());

    }

    @Test
    public void testAddChild() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        n.addChild(n2);

    }
    @Test
    public void testGetChildren() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode("50");
        n.addChild(n2);
        List<TreeNode> testComparison = new ArrayList<TreeNode>();
        testComparison.add(0,n2);
        assertEquals(testComparison, n.getChildren());

    }

    @Test
    public void testGetElement() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        assertEquals("B", n.getElement());
    }

    @Test
    public void testSetElement() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        n.setElement("C");
        assertEquals("C", n.getElement());

    }

    @Test
    public void testGetFirstChild() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        assertEquals(null, n.getFirstChild());

    }

    @Test
    public void testGetFirstChildNonNull() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode("50");
        n.addChild(n2);
        assertEquals(n2, n.getFirstChild());

    }
    @Test
    public void testSetChild() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode("50");
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        n.addChild(n2);
        n.setChild(n3);
        assertEquals(n3, n.getFirstChild());


    }

    @Test
    public void testAddTwoChilds() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        n.addChild(n2);
        n.addChild(n3);


    }

    @Test
    public void testSetNextSibling() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        n.setNextSibling(n2);

    }

    @Test
    public void testGetNextSibling() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        n.setNextSibling(n2);
        assertEquals(n2,n.getNextSibling());

    }

    @Test
    public void testGetTwoChilds() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        n.addChild(n2);
        n.addChild(n3);
        List<TreeNode> testComparison = new ArrayList<TreeNode>();
        testComparison.add(0,n2);
        testComparison.add(1, n3);
        assertEquals(testComparison,n.getChildren());
    }

    @Test
    public void testCount() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        n.addChild(n2);
        n.addChild(n3);
        assertEquals(3,n.count());


    }

    @Test
    public void testSize() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        n.addChild(n2);
        n.addChild(n3);
        assertEquals(2,n.size());

    }

    @Test
    public void testHeight() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        n.addChild(n2);
        n.addChild(n3);
        assertEquals(1,n.height());


    }
    @Test
    public void testHeightTwoDeep() throws Exception {
        TreeNode<String> n = new MyTreeNode("B");
        TreeNode<String> n2 = new MyTreeNode(50);
        TreeNode<String> n3 = new MyTreeNode("DOOOM!");
        TreeNode<String> n4 = new MyTreeNode("DOO00OOM!");
        n.addChild(n2);
        n.addChild(n3);
        n3.addChild(n4);
        assertEquals(2, n.height());


    }

    @Test
    public void testHeightThreeDeep() throws Exception {
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
        assertEquals(3, n.height());

    }
}

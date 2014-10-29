package com.futuramerlin.ember.DataType.HW2Tree;

/**
 * Created by elliot on 6 October 14.
 */

import java.util.List;

/**
 * Represents a single node in a tree.
 */
//Based on http://cs.umaine.edu/~chaw/cos226/code/TreeNode.java
public interface TreeNode<E> {
    /**
     * @return This node's element
     */
    public E getElement();

    /**
     * Set this node's element to the argument.
     *
     * @param elem
     *            The element encapsulated by this node.
     */
    public void setElement(E elem);

    /**
     * Makes the given node a child of this node.
     *
     * @param child
     *            The node to add as a child; it must not be null.
     */
    public void setChild(TreeNode<E> child);

    /**
     * @return The child that is directly below this node or null if none
     *         exists.
     */
    public TreeNode<E> getFirstChild();

    /**
     * @return All of this node's descendents that are directly below this node
     *         in the tree's hierarchy. Children in the list should be ordered
     *         according to the next sibling relationship.
     */
    public List<TreeNode<E>> getChildren();

    /**
     * Makes the known next sibling of this node <code>sibling</code>.
     *
     * @param sibling
     *            Will become this node's sibling; it must not be null.
     */
    public void setNextSibling(TreeNode<E> sibling);

    /**
     * @return The sibling of this node or null if none exists.
     */
    public TreeNode<E> getNextSibling();

    /**
     * @return The number of nodes belonging to this node's subtree, including
     *         this node.
     */
    public int size();

    /**
     * The height of any node is 1 more than the height of its maximum-height
     * child.
     *
     * @return The height of this node in the tree.
     */
    public int height();

    /**
     * @return The elements in the subtree rooted at this node in an order that
     *         ensures parents occur before any of their children.
     */
    public List<TreeNode<E>> getPreOrder();

    /**
     * @return The elements in the subtree rooted at this node in an order that
     *         ensures parents occur after all of their children.
     */
    public List<TreeNode<E>> getPostOrder();

    int count();

    void addChild(TreeNode<E> n);

    int inclusiveHeight();

}

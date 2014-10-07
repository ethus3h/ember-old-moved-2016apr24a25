package com.futuramerlin.ember.DataType.TreeHW2;

/**
 * Created by elliot on 6 October 14.
 */
//Based on http://cs.umaine.edu/~chaw/cos226/code/Tree.java
import java.util.List;

/**
 * A tree that follows the first child/next sibling method.  Keep the children of each node in a lin
 * <ul>
 * <li>One node is distinguished as the root.</li>
 * <li>Every node c, except the root, is connected by an edge from exactly one
 * other node p. Node p is c's parent, and c is one of p's children.</li>
 * <li>A unique path traverses from the root to each node. The number of edges
 * that must be followed is the path length.</li>
 * </ul>
 *
 * This can be found on page 596 in Chapter 18 of "Data Structures & Problem
 * Solving Using Java" by Mark Allen Weiss.
 */

public interface Tree<E> {

    /**
     * @return The root of this tree if the tree is nonempty, and null
     *         otherwise.
     */
    public TreeNode<E> getRoot();

    /**
     * @return The number of nodes in this tree which will be >= 0.
     */
    public int size();

    /**
     * @return The length of the path from the root node to the deepest leaf, if
     *         the tree is nonempty, and 0 otherwise.
     *
     * @see #height(TreeNode node)
     * @see #depth(TreeNode node)
     */
    public int height();

    /**
     * @return The elements in the tree in an order that ensures parents
     * are displayed before any of their children have been displayed.
     */
    public List<TreeNode<E>> getPreOrder();

    /**
     * @return The elements in the tree in an order that ensures parents
     * are displayed after all their children have been displayed.
     */
    public List<TreeNode<E>> getPostOrder();

    /**
     * Removes all nodes from this tree.
     */
    public void makeEmpty();

    /**
     * @return true iff this tree has no nodes.
     */
    public boolean isEmpty();

    /**
     * The height of a node in the tree is the length of the path from the node
     * to the deepest leaf in its subtree. See page 596 of the textbook.
     *
     * @param node
     *            The node whose height is desired; it must not be null.
     * @return The height of the node in the tree or -1 if the node is not in
     *         the tree.
     */
    public int height(TreeNode<E> node);

    /**
     * The depth of a node in the tree is the length of the path from the root
     * to the node. See page 596 of the textbook.
     *
     * @param node
     *            The node whose depth is desired; it must not be null.
     * @return The depth of the node in the tree or -1 if the node is not in the
     *         tree.
     */
    public int depth(TreeNode<E> node);

}

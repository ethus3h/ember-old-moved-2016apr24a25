package com.futuramerlin.ember.DataType.TreeHW2;

import java.util.List;

/**
 * Created by elliot on 6 October 14.
 */
public class MyTree<E> implements Tree<E> {
    private TreeNode<E> rootNode;

    public MyTree(TreeNode a) {
        this.rootNode = a;
    }

    @Override
    public TreeNode<E> getRoot() {
        return this.rootNode;
    }

    @Override
    public int size() {
        return this.rootNode.count();
    }

    @Override
    public int height() {
        return 0;
    }

    @Override
    public List<TreeNode<E>> getPreOrder() {
        return null;
    }

    @Override
    public List<TreeNode<E>> getPostOrder() {
        return null;
    }

    @Override
    public void makeEmpty() {

    }

    @Override
    public boolean isEmpty() {
        if(this.rootNode.size() > 1) {
            return false;
        }
        return true;
    }


    @Override
    public int height(TreeNode node) {
        return 0;
    }

    @Override
    public int depth(TreeNode node) {
        return 0;
    }

}

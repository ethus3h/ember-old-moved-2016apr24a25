package com.futuramerlin.ember.DataType.HW2Tree;

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
        int i = this.rootNode.count();
        if(i>1) {
            return i+1;
        }
        return i;
    }

    @Override
    public int height() {
        return 0;
    }

    @Override
    public List<TreeNode<E>> getPreOrder() {
        return this.rootNode.getPreOrder();
    }

    @Override
    public List<TreeNode<E>> getPostOrder() {
        return this.rootNode.getPostOrder();
    }

    @Override
    public void makeEmpty() {

    }

    @Override
    public boolean isEmpty() {
        if(this.rootNode.size() >= 1) {
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

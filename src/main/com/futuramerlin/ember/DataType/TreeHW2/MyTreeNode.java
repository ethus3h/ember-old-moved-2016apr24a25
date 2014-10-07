package com.futuramerlin.ember.DataType.TreeHW2;

import java.util.List;

/**
 * Created by elliot on 6 October 14.
 */
public class MyTreeNode<E> implements TreeNode<E> {
    private TreeNode<E> leftmostChild;

    public MyTreeNode(E element) {
        this.leftmostChild = null;
    }

    @Override
    public E getElement() {
        return null;
    }

    @Override
    public void setElement(E elem) {

    }

    @Override
    public void setChild(TreeNode<E> child) {

    }

    @Override
    public TreeNode<E> getFirstChild() {
        return null;
    }

    @Override
    public List<TreeNode<E>> getChildren() {
        return null;
    }

    @Override
    public void setNextSibling(TreeNode<E> sibling) {

    }

    @Override
    public TreeNode<E> getNextSibling() {
        return null;
    }

    @Override
    public int size() {
        return 0;
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
    public int count() {
        int i = 1;
        if ( this.getChildren() != null) {
            if (this.getChildren().size() > 0) {
                for (TreeNode<E> node : this.getChildren()) {
                    i = i + node.count();
                    i++;
                }
            }
        }
        return i;
    }

    @Override
    public void addChild(TreeNode<E> n) {
        if(this.count() == 1) {
            this.leftmostChild = n;
        }
    }
}

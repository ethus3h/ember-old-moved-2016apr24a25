package com.futuramerlin.ember.DataType.TreeHW2;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by elliot on 6 October 14.
 */
public class MyTreeNode<E> implements TreeNode<E> {
    private E element;
    private TreeNode<E> leftmostChild;
    private TreeNode<E> nextSibling;

    public MyTreeNode(E element) {
        this.leftmostChild = null;
        this.element = element;
    }

    @Override
    public E getElement() {
        return this.element;
    }

    @Override
    public void setElement(E element) {
        this.element = element;
    }

    @Override
    public void setChild(TreeNode<E> child) {
        this.leftmostChild = child;
    }

    @Override
    public TreeNode<E> getFirstChild() {
        return this.leftmostChild;
    }

    @Override
    public List<TreeNode<E>> getChildren() {
        List<TreeNode<E>> l = new ArrayList<TreeNode<E>>();
        if (!(this.leftmostChild == null)) {
            l.add(this.leftmostChild);
            if(!(this.leftmostChild.getChildren() == null)) {
                l.addAll(this.leftmostChild.getChildren());
            }
        }
        if (!(this.getNextSibling() == null)) {
            l.add(this.getNextSibling());
            if(!(this.getNextSibling().getChildren() == null)) {
                l.addAll(this.getNextSibling().getChildren());
            }
        }
        return l;
    }

    @Override
    public void setNextSibling(TreeNode<E> sibling) {
        this.nextSibling = sibling;
    }

    @Override
    public TreeNode<E> getNextSibling() {
        return this.nextSibling;
    }

    @Override
    public int size() {
        return this.count()-1;
    }

    @Override
    public int height() {
        int i = 0;
        if (!(this.leftmostChild == null)) {
            i = 1;
            if(this.leftmostChild.inclusiveHeight() >= i) {
                i = i+this.leftmostChild.inclusiveHeight();
            }
        }
        return i;
    }

    @Override
    public List<TreeNode<E>> getPreOrder() {
        List<TreeNode<E>> l = new ArrayList<TreeNode<E>>();
        ArrayList e = new ArrayList<E>();
        e.add(this.getElement());
        if (!(this.leftmostChild == null)) {
            l.add(this.leftmostChild);
            if(!(this.leftmostChild.getChildren() == null)) {
                l.addAll(this.leftmostChild.getChildren());
            }
        }
        if (!(this.getNextSibling() == null)) {
            l.add(this.getNextSibling());
            if(!(this.getNextSibling().getChildren() == null)) {
                l.addAll(this.getNextSibling().getChildren());
            }
        };
        for (TreeNode<E> element: l) {
            e.add(element.getElement());
        }
        return e;
    }

    @Override
    public List<TreeNode<E>> getPostOrder() {
        List<TreeNode<E>> l = new ArrayList<TreeNode<E>>();
        ArrayList e = new ArrayList<E>();
        e.add(this.getElement());
        if (!(this.getNextSibling() == null)) {
            l.add(this.getNextSibling());
            if(!(this.getNextSibling().getChildren() == null)) {
                l.addAll(this.getNextSibling().getChildren());
            }
        };
        if (!(this.leftmostChild == null)) {
            l.add(this.leftmostChild);
            if(!(this.leftmostChild.getChildren() == null)) {
                l.addAll(this.leftmostChild.getChildren());
            }
        }
        for (TreeNode<E> element: l) {
            e.add(element.getElement());
        }
        return e;

    }

    @Override
    public int count() {
        if ( this.getChildren() != null) {
            return this.getChildren().size()+1;
        }
        return 1;
    }

    @Override
    public void addChild(TreeNode<E> n) {
        if(this.count() == 1) {
            this.leftmostChild = n;
        }
        else {
            this.leftmostChild.setNextSibling(n);
        }
    }

    @Override
    public int inclusiveHeight() {
        int i = this.height();
        if (!(this.nextSibling == null))
        {
            if (this.getNextSibling().height() > i) {
                i = this.getNextSibling().height();
            }
        }
        return i;
    }
}

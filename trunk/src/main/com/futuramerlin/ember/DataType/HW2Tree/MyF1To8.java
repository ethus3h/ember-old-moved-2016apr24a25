package com.futuramerlin.ember.DataType.HW2Tree;

import java.util.function.Function;

/**
 * Created by elliot on 7 October 14.
 */
public class MyF1To8 implements F1To8 {

    @Override
    public Tree f_1(Tree t) {
        if(t.getRoot().getFirstChild() != null) {
            //Has left subtree
            if(t.getRoot().getFirstChild().getNextSibling() != null) {
                //Has right subtree
                if(t.getRoot().getFirstChild().getNextSibling().getNextSibling() == null) {
                    //Has only 2 children
                    Tree n = new MyTree(null);
                    //Add right subtree
                    n.getRoot().addChild(n.getRoot().getFirstChild().getNextSibling());
                    //Add left subtree
                    n.getRoot().addChild(n.getRoot().getFirstChild());
                }
            }
        }
        return null;
    }

    @Override
    public Tree f_2(Tree t) {
        if(t.getRoot().getFirstChild() != null) {
            //Has left subtree
            if(t.getRoot().getFirstChild().getNextSibling() != null) {
                //Has right subtree
                if(t.getRoot().getFirstChild().getNextSibling().getNextSibling() == null) {
                    //Has only 2 children
                    Tree n = new MyTree(null);
                    //Add right subtree
                    Tree tr = new MyTree(null);
                    tr.getRoot().addChild(n.getRoot().getFirstChild().getNextSibling());
                    n.getRoot().addChild(f_2(tr).getRoot());
                    //Add left subtree
                    Tree tl = new MyTree(null);
                    tl.getRoot().addChild(n.getRoot().getFirstChild());
                    n.getRoot().addChild(f_2(tl).getRoot());
                }
            }
        }
        return null;
    }

    @Override
    public Tree f_3(Tree t) {
        if(t.getRoot().getFirstChild() != null) {
            //Has left subtree
            if(t.getRoot().getFirstChild().getNextSibling() != null) {
                //Has right subtree
                if(t.getRoot().getFirstChild().getNextSibling().getNextSibling() == null) {
                    //Has only 2 children
                    Tree n = new MyTree(null);
                    //Add right subtree
                    Tree tr = new MyTree(null);
                    tr.getRoot().addChild(n.getRoot().getFirstChild().getNextSibling());
                    n.getRoot().addChild(f_3(tr).getRoot());
                    //Add left subtree
                    n.getRoot().addChild(n.getRoot().getFirstChild());
                }
            }
        }
        return null;
    }

    @Override
    public Tree f_4(Tree t) {
        if(t.getRoot().getFirstChild() != null) {
            //Has left subtree
            if(t.getRoot().getFirstChild().getNextSibling() != null) {
                //Has right subtree
                if(t.getRoot().getFirstChild().getNextSibling().getNextSibling() == null) {
                    //Has only 2 children
                    Tree n = new MyTree(null);
                    //Add right subtree
                    n.getRoot().addChild(n.getRoot().getFirstChild().getNextSibling());
                    //Add left subtree
                    Tree tl = new MyTree(null);
                    tl.getRoot().addChild(n.getRoot().getFirstChild());
                    n.getRoot().addChild(f_4(tl).getRoot());
                }
            }
        }
        return null;
    }

    @Override
    public Tree f_5(Tree t) {
        return null;
    }

    @Override
    public Tree f_6(Tree t) {
        return null;
    }

    @Override
    public Tree f_7(Tree t) {
        return null;
    }

    @Override
    public Tree f_8(Tree t) {
        return null;
    }

    @Override
    public Tree g_1(Tree t, Function f) {
        /*if(t.getRoot().getFirstChild() != null) {
            //Has left subtree
            if(t.getRoot().getFirstChild().getNextSibling() != null) {
                //Has right subtree
                if(t.getRoot().getFirstChild().getNextSibling().getNextSibling() == null) {
                    //Has only 2 children
                    Tree n = new MyTree(null);
                    //Add left subtree
                    Tree tl = new MyTree(null);
                    tl.getRoot().addChild(n.getRoot().getFirstChild());
                    n.getRoot().addChild(f(g_1(tl,f)).getRoot());
                    //Add right subtree
                    Tree tr = new MyTree(null);
                    tr.getRoot().addChild(n.getRoot().getFirstChild().getNextSibling());
                    n.getRoot().addChild(f(g_1(tr,f)).getRoot());
                }
            }
        }*/
        return null;
    }
}

package com.futuramerlin.ember.DataType;

import com.futuramerlin.ember.DataProcessor.HashGenerator;
import com.futuramerlin.ember.Throwable.hashSetItemNotFoundException;
import com.futuramerlin.ember.Throwable.hashSetNullArgumentException;
import org.junit.Test;
import org.junit.Assert;

/**
 * Created by elliot on 14 September 14.
 */
public class HashSetTest {
    @Test
    public void testHashSet() throws Exception {
        HashSet t = new HashSet();

    }

    @Test
    public void testHashSetAdd() throws Exception {
        HashSet t = new HashSet();
        t.allocateArray(15);
        String a = new String("doom");
        t.add(a);


    }

    @Test
    public void testHashSetClear() throws Exception {
        HashSet t = new HashSet();
        t.allocateArray(15);
        String a = new String("doom");
        t.add(a);
        t.clear();

    }

 /*   @Test
    public void testHashSetContains() throws Exception {
        HashSet t = new HashSet();

        t.allocateArray(15);
        String a = new String("doom");
        t.add(a);
        Assert.assertTrue(t.contains(a));

    }*/

   /* @Test
    public void testHashSetContainsFalse() throws Exception {
        HashSet t = new HashSet();
        t.allocateArray(15);

        String a = new String("doom");
        t.add(a);
        String b = new String("mood");

        Assert.assertFalse(t.contains(b));


    }*/


    @Test
    public void testAllocateArray() throws Exception {
        HashSet t = new HashSet();
        t.allocateArray(15);
        Assert.assertArrayEquals(new HashEntry[15], t.array);

    }

    @Test(expected=hashSetNullArgumentException.class)
    public void testFindPos() throws Exception, hashSetItemNotFoundException, hashSetNullArgumentException {

        HashSet t = new HashSet();
        t.findPos(null);

    }

    @Test(expected=hashSetItemNotFoundException.class)
    public void testFindPosNonexistent() throws Exception, hashSetItemNotFoundException, hashSetNullArgumentException {

        HashSet t = new HashSet();
        t.findPos(new HashGenerator());
    }

    @Test
    public void testFindPosFirstItem() throws Exception, hashSetItemNotFoundException, hashSetNullArgumentException {
        HashSet t = new HashSet();
        HashGenerator a = new HashGenerator();
        Assert.assertEquals(0,t.findPos(a));

    }
    /*   @Test
    public void testFindPos() throws Exception, hashSetItemNotFoundException {
        HashSet t = new HashSet();
        t.allocateArray(15);
        String a = new String("doom");

        t.add(a);
        Assert.assertEquals(0,t.findPos(a));

    }
    @Test
    public void testFindPosSecond() throws Exception, hashSetItemNotFoundException {
        HashSet t = new HashSet();
        t.allocateArray(15);
        String a = new String("doom");
        String b = new String("mood");
        t.add(a);
        t.add(b);

        Assert.assertEquals(1,t.findPos(b));

    }*/

    /*@Test
    public void testHashSetIsActive() throws Exception, hashSetItemNotFoundException, hashSetNullArgumentException {
        HashSet t = new HashSet();
        String a = new String("doom");
        t.allocateArray(15);
        boolean isActive = t.isActive(t.array, t.findPos(a));


    }*/

    @Test
    public void testHashSetIncrementIndex() throws Exception {
        HashSet t = new HashSet();
        t.increment();

    }
    @Test
    public void testHashSetIndex() throws Exception {
        HashSet t = new HashSet();
        Assert.assertEquals(t.currentIndex,0);

    }

    @Test
    public void testHashSetIncrementFirst() throws Exception {
        HashSet t = new HashSet();
        t.increment();
        Assert.assertEquals(t.currentIndex,1);
    }

}

package com.futuramerlin.ember.DataProcessor;

import org.junit.Assert;
import org.junit.Test;

/**
 * Created by elliot on 16 September 14.
 */
public class ASCIIHashGeneratorTest {

    @Test
    public void testNewASCIIHashGenerator() throws Exception {
        ASCIIHashGenerator a = new ASCIIHashGenerator();

    }

    @Test
    public void testHashC20F1() throws Exception {
        ASCIIHashGenerator a = new ASCIIHashGenerator();
        Assert.assertEquals(5, a.C20F1("Sample", 17));


    }
}

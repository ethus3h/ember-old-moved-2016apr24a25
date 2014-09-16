package com.futuramerlin.ember.DataProcessor;

import org.junit.Assert;
import org.junit.Test;

import java.security.MessageDigest;

/**
 * Created by PermissionGiver on 8/16/14.
 */
public class DataProcessorTest {
    @Test
    public void testBin2hex() throws Exception {
        byte[] bytes = MessageDigest.getInstance("MD5").digest("Hello, World!".getBytes("UTF-8"));
        DataProcessor d = new DataProcessor();
        String hex = d.bin2hex(bytes);
        Assert.assertEquals("65a8e27d8879283831b664bd8b7f0ad4", hex);
    }

    @Test
    public void testLong2hex() throws Exception {
        DataProcessor d = new DataProcessor();
        long n = 3510043186L;
        Assert.assertEquals("d1370232", d.dec2hex(n));
    }

    @Test
    public void testLong2hexC() throws Exception {
        DataProcessor d = new DataProcessor();
        long n = 3510043186L;
        Assert.assertNotEquals("D1370232", d.dec2hex(n));
    }

    @Test
    public void testInt2hex() throws Exception {
        DataProcessor d = new DataProcessor();
        int n = 35100431;
        Assert.assertEquals("217970f", d.dec2hex(n));
    }
}

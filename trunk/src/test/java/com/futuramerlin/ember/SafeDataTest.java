package com.futuramerlin.ember;

import org.junit.Assert;
import org.junit.Test;
import src.Throwable.CorruptedSafeDataException;

public class SafeDataTest {
    public SafeDataTest() {
    }

    @Test
    public void testNewSafeData() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
    }

    @Test
    public void testSDLength() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals(d.length, "Hello, World!".length());
    }

    @Test
    public void testSDmd5() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals("65a8e27d8879283831b664bd8b7f0ad4", d.md5);

    }

    @Test
    public void testSDsha() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals("0a0a9f2a6772942557ab5355d76af442f8f65e01", d.sha);

    }

    @Test
    public void testSDs29() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals("374d794a95cdcfd8b35993185fef9ba368f160d8daf432d08ba9f1ed1e5abe6cc69291e0fa2fe0006a52570ef18c19def4e617c33ce52ef0a6e5fbe318cb0387", d.s29);


    }

    @Test
    public void testSDcrc() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals("ec4ac3d0", d.crc);


    }
    @Test
        public void testSDCheck() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.check();
    }
    @Test
    public void testSDNukeMD5() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukemd5();
    }
    @Test
    public void testSDNukeSHA() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukesha();
    }
    @Test
    public void testSDNukeS29() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukes29();
    }
    @Test
    public void testSDNukeCRC() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukecrc();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkMD5() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukemd5();
        d.check();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkMD5i() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukemd5();
        d.checkmd5();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkSHA() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukesha();
        d.check();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkSHAi() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukesha();
        d.checksha();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkS29() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukes29();
        d.check();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkS29i() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukes29();
        d.checks29();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkCRC() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukecrc();
        d.check();
    }
    @Test(expected=CorruptedSafeDataException.class)
    public void testSDCkCRCi() throws Exception, CorruptedSafeDataException {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        d.nukecrc();
        d.checkcrc();
    }
}
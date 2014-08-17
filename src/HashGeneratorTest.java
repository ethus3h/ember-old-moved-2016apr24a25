package src;

import org.junit.*;
import org.junit.Test;

import java.security.MessageDigest;

public class HashGeneratorTest {
    public HashGeneratorTest() {
    }

    @org.junit.Test
    public void testCreateHashGenerator() throws Exception {
        HashGenerator h = new HashGenerator();


    }

    @Test
    public void testCreateMD5Sum() throws Exception {
        HashGenerator h = new HashGenerator();
        h.md5("Hello, World!".getBytes("UTF-8"));

    }

    @Test
    public void testMd5toHex() throws Exception {
        byte[] md5bytes = MessageDigest.getInstance("MD5").digest("Hello, World!".getBytes("UTF-8"));
        HashGenerator h = new HashGenerator();
        String md5 = h.md5toHex(md5bytes);
        Assert.assertEquals("65a8e27d8879283831b664bd8b7f0ad4", md5);
    }

    @Test
    public void testMd5Sum() throws Exception {
        HashGenerator h = new HashGenerator();
        Assert.assertEquals("65a8e27d8879283831b664bd8b7f0ad4", h.md5("Hello, World!".getBytes("UTF-8")));
    }

    @Test
    public void testCreateSHA() throws Exception {
        HashGenerator h = new HashGenerator();
        String sha = h.sha("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals("0a0a9f2a6772942557ab5355d76af442f8f65e01", sha);
    }
}
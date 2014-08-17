package src;

import org.junit.Assert;
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
    public void testBin2hex() throws Exception {
        byte[] md5bytes = MessageDigest.getInstance("MD5").digest("Hello, World!".getBytes("UTF-8"));
        HashGenerator h = new HashGenerator();
        String md5 = h.bin2hex(md5bytes);
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

    @Test
    public void testCreateS29() throws Exception {
        HashGenerator h = new HashGenerator();
        String s29 = h.s29("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals("374d794a95cdcfd8b35993185fef9ba368f160d8daf432d08ba9f1ed1e5abe6cc69291e0fa2fe0006a52570ef18c19def4e617c33ce52ef0a6e5fbe318cb0387", s29);
    }

    @Test
    public void testCreateCRC() throws Exception {
        HashGenerator h = new HashGenerator();
        String crc = h.crc("Hello, World!".getBytes("UTF-8"));
        Assert.assertEquals("ec4ac3d0", crc);
    }
}
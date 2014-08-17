package src;

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

}

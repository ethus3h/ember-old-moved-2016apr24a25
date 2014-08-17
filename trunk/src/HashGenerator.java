package src;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

/**
 * Created by PermissionGiver on 8/16/14.
 */
public class HashGenerator {


    private final DataProcessor dataProcessor = new DataProcessor();

    public String
    md5(byte[] data) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("MD5");
        byte[] md5bytes = md.digest(data);
        return dataProcessor.bin2hex(md5bytes);
    }

    public String bin2hex(byte[] md5bytes) {
        return dataProcessor.bin2hex(md5bytes);
    }

    public String sha(byte[] data) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("SHA-1");
        byte[] shabytes = md.digest(data);
        String sha = dataProcessor.bin2hex(shabytes);
        return sha;
    }

    public String s29(byte[] data) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("SHA-512");
        byte[] s29bytes = md.digest(data);
        String s29 = dataProcessor.bin2hex(s29bytes);
        return s29;
    }
}

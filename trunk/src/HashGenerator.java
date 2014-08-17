package src;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import org.apache.commons.codec.binary.Hex;

/**
 * Created by PermissionGiver on 8/16/14.
 */
public class HashGenerator {


    public String
    md5(byte[] bytes) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("MD5");
        byte[] md5bytes = md.digest(bytes);
        return md5toHex(md5bytes);
    }

    public String md5toHex(byte[] md5bytes) {
        Hex encoder = new Hex();
        String s = Hex.encodeHexString(md5bytes);
        return s;
    }

    public String sha(byte[] bytes) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("SHA-1");
        byte[] shabytes = md.digest(bytes);
        String sha = md5toHex(shabytes);
        return sha;
    }
}

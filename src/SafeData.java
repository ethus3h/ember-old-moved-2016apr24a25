package src;

import java.security.NoSuchAlgorithmException;

/**
 * Created by PermissionGiver on 8/16/14.
 */
public class SafeData {

    public int length;
    public String md5;
    public String sha;
    public String s29;

    public SafeData(byte[] s) throws NoSuchAlgorithmException {
        byte[] data = s;
        length = data.length;
        HashGenerator h = new HashGenerator();
        md5 = h.md5(data);
        sha = h.sha(data);
        s29 = h.s29(data);

    }
}

package src;

import org.apache.commons.codec.binary.Hex;

public class DataProcessor {
    public DataProcessor() {
    }

    public String bin2hex(byte[] md5bytes) {
        Hex encoder = new Hex();
        String s = Hex.encodeHexString(md5bytes);
        return s;
    }
}
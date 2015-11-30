package com.futuramerlin.ember.Common.DataProcessor;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.zip.CRC32;
import java.util.zip.Checksum;

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
        return dataProcessor.bin2hex(shabytes);
    }

    public String s29(byte[] data) throws NoSuchAlgorithmException {
        MessageDigest md = MessageDigest.getInstance("SHA-512");
        byte[] s29bytes = md.digest(data);
        return dataProcessor.bin2hex(s29bytes);
    }

    public String crc(byte[] data) throws NoSuchAlgorithmException {
        /* help from http://www.java-examples.com/generate-crc32-checksum-byte-array-example */
        Checksum checksum = new CRC32();
        checksum.update(data, 0, data.length);
        long lngChecksum = checksum.getValue();
        return dataProcessor.dec2hex(lngChecksum);
    }
}

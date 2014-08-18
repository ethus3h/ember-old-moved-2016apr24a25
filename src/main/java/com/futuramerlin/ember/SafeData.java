package com.futuramerlin.ember;


import com.futuramerlin.ember.Throwable.CorruptedSafeDataException;

import java.security.NoSuchAlgorithmException;

/**
 * Created by PermissionGiver on 8/16/14.
 */
public class SafeData {

    public int length;
    public String md5;
    public String sha;
    public String s29;
    public String crc;
    public byte[] data;

    public SafeData(byte[] s) throws NoSuchAlgorithmException {
        data = s;
        length = data.length;
        HashGenerator h = new HashGenerator();
        md5 = h.md5(data);
        sha = h.sha(data);
        s29 = h.s29(data);
        crc = h.crc(data);

    }

    public void nukemd5() {
        md5 = "";
    }

    public void nukesha() {
        sha = "";
    }

    public void nukes29() {
        s29 = "";
    }

    public void nukecrc() {
        crc = "";
    }

    public void check() throws NoSuchAlgorithmException, CorruptedSafeDataException {
        checkmd5();
        checksha();
        checks29();
        checkcrc();
    }

    public void checkmd5() throws NoSuchAlgorithmException, CorruptedSafeDataException {
        HashGenerator h = new HashGenerator();
        if(! this.md5.equals(h.md5(data))){
            throw new CorruptedSafeDataException();
        }
    }

    public void checksha() throws NoSuchAlgorithmException, CorruptedSafeDataException {
        HashGenerator h = new HashGenerator();
        if(! this.sha.equals(h.sha(data))){
            throw new CorruptedSafeDataException();
        }
    }

    public void checks29() throws NoSuchAlgorithmException, CorruptedSafeDataException {
        HashGenerator h = new HashGenerator();
        if(! this.s29.equals(h.s29(data))){
            throw new CorruptedSafeDataException();
        }
    }

    public void checkcrc() throws NoSuchAlgorithmException, CorruptedSafeDataException {
        HashGenerator h = new HashGenerator();
        if(! this.crc.equals(h.crc(data))){
            throw new CorruptedSafeDataException();
        }
    }
}

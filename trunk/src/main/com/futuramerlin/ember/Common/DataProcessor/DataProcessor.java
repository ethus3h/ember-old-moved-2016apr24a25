package com.futuramerlin.ember.Common.DataProcessor;

import org.apache.commons.codec.binary.Hex;

public class DataProcessor {
    public DataProcessor() {
    }

    public String bin2hex(byte[] md5bytes) {
        Hex encoder = new Hex();
        return encoder.encodeHexString(md5bytes);
    }

    public String dec2hex(long n) {
        return Long.toHexString(n).toLowerCase();
    }

}
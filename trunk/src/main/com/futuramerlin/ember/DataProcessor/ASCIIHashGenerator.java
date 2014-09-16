package com.futuramerlin.ember.DataProcessor;

/**
 * Created by elliot on 16 September 14.
 */
public class ASCIIHashGenerator {
    public int C20F1(String key, int tableSize) {
        System.out.println("Starting hash of "+key);
        int hashVal = 0;
        for (int i = 0; i < key.length(); i++) {
            hashVal = (hashVal * 128 + key.charAt(i)) % tableSize;
            System.out.println("hashVal: "+hashVal);
        }
        System.out.println("Finished hash");
        return hashVal;
    }
}

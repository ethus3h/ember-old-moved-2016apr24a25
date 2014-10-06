package com.futuramerlin.ember.DataProcessor;

/**
 * Created by elliot on 16 September 14.
 */
//Non-TDD code based on textbook
public class ASCIIHashGenerator {
    public int C20F1(String key, int tableSize) {
        System.out.println("Starting 20.1 hash of "+key);
        int hashVal = 0;
        for (int i = 0; i < key.length(); i++) {
            hashVal = (hashVal * 128 + key.charAt(i)) % tableSize;
            System.out.println("hashVal: "+hashVal);
        }
        System.out.println("Finished hash");
        return hashVal;
    }

    public int C20F2(String key, int tableSize) {
        System.out.println("Starting 20.2 hash of "+key);
        int hashVal = 0;
        for (int i = 0; i < key.length(); i++) {
            hashVal = 37 * hashVal + key.charAt(i);
            System.out.println("hashVal: "+hashVal);
        }
        hashVal %= tableSize;
        if(hashVal <0)
        {
            hashVal += tableSize;
        }
        System.out.println("Wrapped hashVal: "+hashVal);
        System.out.println("Finished hash");
        return hashVal;
    }
    public int C20F2alt(String key, int tableSize) {
        System.out.println("Starting 20.2 alt hash of "+key);
        int hashVal = 0;
        for (int i = 0; i < key.length(); i++) {
            hashVal = 37 * hashVal + key.charAt(i);
            hashVal %= tableSize;
            System.out.println("hashVal: "+hashVal);
        }
        if(hashVal <0)
        {
            hashVal += tableSize;
        }
        System.out.println("Wrapped hashVal: "+hashVal);
        System.out.println("Finished hash");
        return hashVal;
    }
    public int C20F3(String key, int tableSize) {
        System.out.println("Starting 20.3 hash of "+key);
        int hashVal = 0;
        for (int i = 0; i < key.length(); i++) {
            hashVal += key.charAt(i);
            System.out.println("hashVal: "+hashVal);
        }
        System.out.println("Finished hash value: "+hashVal % tableSize);
        System.out.println("Finished hash");
        return hashVal % tableSize;
    }
}

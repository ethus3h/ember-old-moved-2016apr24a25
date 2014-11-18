package com.futuramerlin.ember.Client;

import java.util.ArrayList;

/**
 * Created by elliot on 14.11.18.
 */
public class RecordManager {
    public ArrayList<Integer> records;

    void RecordManager() {
        this.records = null;
    }

    public void add(Integer idx, Integer val) {
        this.records.add(idx,val);
    }
}

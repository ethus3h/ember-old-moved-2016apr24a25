package com.futuramerlin.ember.Common.DataProcessor;

import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;

/**
 * Created by elliot on 14.11.01.
 */
public class StringProcessor {
    public String removeLastCharacter(String s) throws ZeroLengthInputException {
        //from http://stackoverflow.com/questions/7438612/how-to-remove-the-last-character-from-a-string
        if(s.length() == 0) {
            throw new ZeroLengthInputException();
        }
        return s.substring(0,s.length()-1);
    }
}

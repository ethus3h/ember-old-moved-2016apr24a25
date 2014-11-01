package com.futuramerlin.ember.Common.DataProcessor;

import com.futuramerlin.ember.Common.Exception.ZeroLengthInputException;
import org.junit.Test;

/**
 * Created by elliot on 14.11.01.
 */
public class StringProcessorTest {
    @Test
    public void testNewStringProcessor() throws Exception {
        StringProcessor p = new StringProcessor();


    }
    @Test
    public void testRemoveLastCharacter() throws Exception, ZeroLengthInputException {
        StringProcessor p = new StringProcessor();
        String s = "Doom!?";
        org.junit.Assert.assertEquals(p.removeLastCharacter(s),"Doom!");

    }
    @Test(expected=ZeroLengthInputException.class)
    public void testRemoveLastCharacterZero() throws Exception, ZeroLengthInputException {
        StringProcessor p = new StringProcessor();
        String s = "";
        org.junit.Assert.assertEquals(p.removeLastCharacter(s), "");
    }
    @Test
    public void testRemoveLastCharacterLenOne() throws Exception, ZeroLengthInputException {
        StringProcessor p = new StringProcessor();
        String s = "?";
        org.junit.Assert.assertEquals(p.removeLastCharacter(s),"");

    }

}

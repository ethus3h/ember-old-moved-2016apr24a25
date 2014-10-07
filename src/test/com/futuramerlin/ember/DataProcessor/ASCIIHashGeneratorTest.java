package com.futuramerlin.ember.DataProcessor;

import org.junit.Assert;
import org.junit.Test;

/**
 * Created by elliot on 16 September 14.
 */
public class ASCIIHashGeneratorTest {

    @Test
    public void testNewASCIIHashGenerator() throws Exception {
        ASCIIHashGenerator a = new ASCIIHashGenerator();

    }

    @Test
    public void testHashC20F1() throws Exception {
        ASCIIHashGenerator a = new ASCIIHashGenerator();
        Assert.assertEquals(5, a.C20F1("Sample", 17));

        a.C20F1("The", 17);
        a.C20F1("vorpal", 17);
        a.C20F1("blade", 17);
        a.C20F1("went", 17);
        a.C20F1("snicker-snack!", 17);
        a.C20F1("He", 17);
        a.C20F1("went", 17);
        a.C20F1("galumphing", 17);
        a.C20F1("back.", 17);


    }

    @Test
    public void testHashC20F2() throws Exception {
        ASCIIHashGenerator a = new ASCIIHashGenerator();
        Assert.assertEquals(16, a.C20F2("Sample", 17));

        a.C20F2("The", 17);
        a.C20F2("vorpal", 17);
        a.C20F2("blade", 17);
        a.C20F2("went", 17);
        a.C20F2("snicker-snack!", 17);
        a.C20F2("He", 17);
        a.C20F2("went", 17);
        a.C20F2("galumphing", 17);
        a.C20F2("back.", 17);


    }

    @Test
    public void testHashC20F2alt() throws Exception {
        ASCIIHashGenerator a = new ASCIIHashGenerator();
        Assert.assertEquals(0, a.C20F2alt("Sample", 17));

        a.C20F2alt("The", 17);
        a.C20F2alt("vorpal", 17);
        a.C20F2alt("blade", 17);
        a.C20F2alt("went", 17);
        a.C20F2alt("snicker-snack!", 17);
        a.C20F2alt("He", 17);
        a.C20F2alt("went", 17);
        a.C20F2alt("galumphing", 17);
        a.C20F2alt("back.", 17);


    }

/*    @Test
    public void testHashC20F3() throws Exception {
        ASCIIHashGenerator a = new ASCIIHashGenerator();
        Assert.assertEquals(15, a.C20F3("Sample", 17));

        a.C20F3("The", 17);
        a.C20F3("vorpal", 17);
    }*/
}
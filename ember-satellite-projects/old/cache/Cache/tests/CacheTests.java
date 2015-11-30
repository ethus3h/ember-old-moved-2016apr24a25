/**
 * Created by elliot on 14.11.29.
 */
public class CacheTests {
    @org.junit.Test
    public void testCaches() throws Exception {
        Cache c = new Cache("http://archive.org/\n");
        System.out.println(c.get(1));

    }
}

package src.Server;

import org.junit.Test;

import static org.junit.Assert.assertEquals;

public class ServerTest {
    @Test
    public void testCreateServer() throws Exception {
        Server s = new Server();
    }

    @Test
    public void testServerEcho() throws Exception {
        Server s = new Server();
        assertEquals("Hello, World!", s.echo("Hello, World!"));
    }

}
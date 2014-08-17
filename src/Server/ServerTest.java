package src.Server;

import org.junit.Test;
import src.SafeData;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

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

    @Test
    public void testCreateRecordServer() throws Exception {
        RecordServer s = new RecordServer();
    }

    @Test
    public void testCreateFrontEndServer() throws Exception {
        FrontEndServer s = new FrontEndServer();

    }

    @Test
    public void testCreateMetaServer() throws Exception {
        MetaServer s = new MetaServer();

    }

    @Test
    public void testStoreInFrontEndServer() throws Exception {
        FrontEndServer s = new FrontEndServer();
        s.store("Hello, World!".getBytes("UTF-8"));

    }

    @Test
    public void testNewSafeData() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
    }

    @Test
    public void testSDLength() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        assertEquals(d.length,"Hello, World!".length());
    }

    @Test
    public void testSDmd5() throws Exception {
        SafeData d = new SafeData("Hello, World!".getBytes("UTF-8"));
        assertEquals("65a8e27d8879283831b664bd8b7f0ad4",d.md5);

    }

}
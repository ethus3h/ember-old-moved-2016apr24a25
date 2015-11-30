import java.io.*;
import java.net.URL;

/**
 * Created by elliot on 14.11.29.
 */
public class Cache {
    private final String firstPart;
    private final String lastPart;
    //help from https://docs.oracle.com/javase/tutorial/networking/urls/readingURL.html and http://stackoverflow.com/questions/2295221/java-net-url-read-stream-to-byte
    //Methods: Create cache, request document by ID.
    public Cache(String pattern) {
        this.firstPart = pattern.substring(0,pattern.indexOf("\n"));
        this.lastPart = pattern.substring(pattern.indexOf("\n"));
    }
    public byte[] get(Integer id) throws IOException {
/*                BufferedReader in = new BufferedReader(
                new InputStreamReader(new URL(this.firstPart+id+this.lastPart).openStream()));

        String inputLine;
        while ((inputLine = in.readLine()) != null)
            System.out.println(inputLine);
        in.close();*/
        ByteArrayOutputStream bais = new ByteArrayOutputStream();
        InputStream is = null;
        try {
            is = new URL(this.firstPart+id+this.lastPart).openStream();
            byte[] byteChunk = new byte[4096]; // Or whatever size you want to read in at a time.
            int n;

            while ( (n = is.read(byteChunk)) > 0 ) {
                bais.write(byteChunk, 0, n);
            }
        }
        catch (IOException e) {
            System.err.printf ("Failed while reading bytes from %s: %s", new URL(this.firstPart+id+this.lastPart).toExternalForm(), e.getMessage());
            e.printStackTrace ();
            // Perform any other exception handling that's appropriate.
        }
        finally {
            if (is != null) { is.close(); }
        }
        return bais.toByteArray();
    }
}

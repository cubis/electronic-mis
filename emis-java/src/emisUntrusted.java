import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.io.IOException;
import java.io.InputStream;
import java.io.StringReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.security.*;
import java.net.URLEncoder;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.InputSource;

class Login extends JFrame implements ActionListener
{
	
	private static final String GET = "GET";

	private static final long serialVersionUID = 1L;
	
	JButton SUBMIT;
	JPanel panel;
	JLabel label1,label2;
	static JTextField text1;
	final JTextField text2;
	
	Login()
	{
	    label1 = new JLabel();
		label1.setText("Username:");
		text1 = new JTextField(15);

		label2 = new JLabel();
		label2.setText("Password:");
	    text2 = new JPasswordField(15);
	    //this.setLayout(new BorderLayout());
 
		SUBMIT=new JButton("SUBMIT");
		
        panel=new JPanel(new GridLayout(3,1));
		panel.add(label1);
		panel.add(text1);
		panel.add(label2);
		panel.add(text2);
		panel.add(SUBMIT);
	    add(panel,BorderLayout.CENTER);
        SUBMIT.addActionListener(this);
        setTitle("LOGIN FORM");
	}
	
	public void actionPerformed(ActionEvent ae)
	{
		String user = text1.getText();
		String myPw = text2.getText();
		String epw = "";
		
		try {
			/* THIS GARBAGE DOES MD5 FOR YOU */
	    	byte[] bytesOfMessage = myPw.getBytes("UTF-8");

	   		MessageDigest md = MessageDigest.getInstance("MD5");
	    		
	   	    md.update(bytesOfMessage);
	   	    byte[] hash = md.digest();
	   	    String hexString = convertToHex(hash);
    	    epw = hexString;
		} catch (Exception e) {
	 		System.err.println(e);
	    		System.exit(1);
	    }
		
		boolean quiet = false;

        try
        {
            String method = "GET";
            
            URL url = new URL("http://localhost/emis-dev/Authenticate.php?u="+URLEncoder.encode(user)+"&p="+URLEncoder.encode(epw));
            
            if (GET.equalsIgnoreCase(method))
            {
                request(quiet, GET, url, user, myPw, null);
            }
            else {
            	JOptionPane.showMessageDialog(this,"Error. Something went wrong.", "Error", JOptionPane.ERROR_MESSAGE);
            }                                                     
        }
        catch (Exception x)
        {
            System.err.println(x);
            System.exit(1);
        }
	}
	
	public static String getUsername() {
		return(text1.getText());
	}
	
	private static String convertToHex(byte[] data) { 
        StringBuffer buf = new StringBuffer();
        for (int i = 0; i < data.length; i++) { 
            int halfbyte = (data[i] >>> 4) & 0x0F;
            int two_halfs = 0;
            do { 
                if ((0 <= halfbyte) && (halfbyte <= 9)) 
                    buf.append((char) ('0' + halfbyte));
                else 
                    buf.append((char) ('a' + (halfbyte - 10)));
                halfbyte = data[i] & 0x0F;
            } while(two_halfs++ < 1);
        } 
        return buf.toString();
    } 
	
    public static Document loadXMLFromString(String responseBody) throws Exception
    {
        DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
        DocumentBuilder builder = factory.newDocumentBuilder();
        InputSource is = new InputSource(new StringReader(responseBody));
        return builder.parse(is);
    }
    
    private static void request(boolean quiet, String method, URL url, String username, String password, InputStream body)
	    throws IOException
	    {
	        // sigh.  openConnection() doesn't actually open the connection,
	        // just gives you a URLConnection.  connect() will open the connection.
	       
		 	if (!quiet)
	        {
	            System.out.println("[issuing request: " + method + " " + url + "]");
	        }
	        
	        HttpURLConnection connection = (HttpURLConnection)url.openConnection();
	        connection.setRequestMethod(method);

	        byte buffer[] = new byte[8192];
	        int read = 0;
	        long time = System.currentTimeMillis();
	        connection.connect();
	        
	        InputStream responseBodyStream = connection.getInputStream();
	        StringBuffer responseBody = new StringBuffer();
	        while ((read = responseBodyStream.read(buffer)) != -1)
	        {
	            responseBody.append(new String(buffer, 0, read));
	        }
	        connection.disconnect();
	        time = System.currentTimeMillis() - time;
	        
	        // start printing output
	        if (!quiet)
	            System.out.println("[read " + responseBody.length() + " chars in " + time + "ms]");
	        
	        // look at headers
	        // the 0th header has a null key, and the value is the response line ("HTTP/1.1 200 OK" or whatever)
	        if (!quiet)
	        {
	            String header = null;
	            String headerValue = null;
	            int index = 0;
	            while ((headerValue = connection.getHeaderField(index)) != null)
	            {
	                header = connection.getHeaderFieldKey(index);
	                
	                if (header == null)
	                    System.out.println(headerValue);
	                else
	                    System.out.println(header + ": " + headerValue);
	                
	                index++;
	            }
	            System.out.println("");
	        }
	        
	        // dump body
	        System.out.print(responseBody);
	        String message = responseBody.toString();
	        try {
	        	
	        	/* Parse XML Values for result (resultValue) and key (keyValue) */
	        	
				Document myXML = loadXMLFromString(message);
				NodeList resultList = myXML.getElementsByTagName("result");
				Node resultNode = resultList.item(0);
				String resultValue = resultNode.getTextContent().toString();
				System.out.println("\nLogged in result:" + resultValue);
				
				NodeList keyList = myXML.getElementsByTagName("key");
				Node keyNode = keyList.item(0);
				String keyValue = keyNode.getTextContent().toString();
				System.out.println("Key value:" + keyValue);
				
				if(resultValue.equals("1")) {
					EmisSession currentSession = new EmisSession();
					currentSession.setKeyValue(keyValue);
					currentSession.setName(getUsername());
				}
				
			} catch (Exception e) {
				e.printStackTrace();
			}
	        System.out.flush();
	    }
}

public class emisUntrusted {
	
	public static void main(String[] args)
	{
		try
		{
			Login frame=new Login();
			frame.setSize(300,100);
			frame.setVisible(true);
		}
		catch(Exception e)
		{
			JOptionPane.showMessageDialog(null, e.getMessage());
		}
	}

	public emisUntrusted() {
		super();
	}
}
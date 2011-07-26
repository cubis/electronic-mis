using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.Data.Odbc;
using System.IO;
using System.Web;
using System.Security.Cryptography;
using System.Xml;
using System.Diagnostics;
using System.Net.Security;
using System.Security.Cryptography.X509Certificates;

namespace Electronic_MIS
{
    

    public partial class LoginTab : UserControl 
    {
        public delegate void LoginEventHandler(object sender, LoginEventArgs e);
        public event LoginEventHandler LoginEvent;        
        
        public SessionManager sessionManager;

        string Server = "";
        
        protected virtual void OnLoginEvent(LoginEventArgs e)
        {
            if(LoginEvent != null)
            LoginEvent(this, e);
        }

        public LoginTab()
        {
            InitializeComponent();
            sessionManager = new SessionManager();
            serverSelect.SelectedItem = 0;
        }
        
        private void btnLogin_Click(object sender, EventArgs e)
        {
            SetCertificatePolicy();

            Server = serverSelect.SelectedItem.ToString();

            if (Server == ""){
                MessageBox.Show("Please select a server");
                return;
            }


            Cursor.Current = Cursors.WaitCursor;

            //Form Validation
            if (txtUser.Text == "")
            {
                MessageBox.Show("Please Enter Your Username");
                return;
            }
            if (txtPassword.Text == "")
            {
                MessageBox.Show("Please Enter Your Password");
                return;
            }


            //Set the User for the Session Manager
            sessionManager.User = txtUser.Text;

            string pass = CalculateMD5Hash(txtPassword.Text);
            StringBuilder data = new StringBuilder();
            data.Append(Server);
            data.Append("authenticateREST.php");
            data.Append("?u=" + WebUtility.HtmlEncode(txtUser.Text.Replace("'","''")));
            data.Append("&p=" + WebUtility.HtmlEncode(pass.Replace("'","''")));
            
            string url = data.ToString();
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "GET";

            Debug.WriteLine("Connection Request: ");
            Debug.WriteLine(request.RequestUri.OriginalString.ToString());

            try
            {
                WebResponse response = request.GetResponse();

                /*
                StreamReader reader = new StreamReader(response.GetResponseStream());
                Debug.WriteLine("");
                Debug.WriteLine("LOGIN XML:");
                Debug.WriteLine(reader.ReadToEnd());

                response = request.GetResponse();
                //*/
                
                XmlTextReader xmlReader = new XmlTextReader(response.GetResponseStream());

                while (xmlReader.Read())
                {
                    switch (xmlReader.NodeType)
                    {
                        case XmlNodeType.Element:
                            if (xmlReader.Name == "errNum")
                            {
                                if (xmlReader.ReadElementContentAsInt() > 0)
                                {
                                    MessageBox.Show("Login failure.  Please check your login information and try again.");
                                }
                            }
                            else if (xmlReader.Name == "key")
                            {
                                xmlReader.Read();
                                sessionManager.Key = xmlReader.Value;
                            }

                            break;
                        default:
                            break;
                    }
                }

            }
            catch (Exception exp)
            {
                MessageBox.Show(exp.Message, "Yeah...we didn't plan for this", MessageBoxButtons.OK);
                Application.Exit();
            }

            Cursor.Current = Cursors.Default;

            LoginEventArgs eventArgs = new LoginEventArgs(sessionManager);
            eventArgs.Server = Server;
            OnLoginEvent(eventArgs);
        }

        public string CalculateMD5Hash(string input)
        {
            // step 1, calculate MD5 hash from input
            MD5 md5 = System.Security.Cryptography.MD5.Create();
            byte[] inputBytes = System.Text.Encoding.ASCII.GetBytes(input);
            byte[] hash = md5.ComputeHash(inputBytes);


            RSA rsa = RSA.Create();


            // step 2, convert byte array to hex string
            StringBuilder sb = new StringBuilder();
            for (int i = 0; i < hash.Length; i++)
            {
                sb.Append(hash[i].ToString("x2"));
            }
            return sb.ToString();
        }

        public static void SetCertificatePolicy() 
        {
            ServicePointManager.ServerCertificateValidationCallback = RemoteCertificateValidate;
        }
        
        /// <summary>
        /// Remotes the certificate validate./// 
        /// </summary>
        private static bool RemoteCertificateValidate(object sender, X509Certificate cert,X509Chain chain, SslPolicyErrors error)
        {    
            // trust any certificate!!!    
            System.Console.WriteLine("Warning, trust any certificate");    
            return true;
        }
    }

    public class LoginEventArgs : EventArgs
    {
        SessionManager session;
        string server;

        public LoginEventArgs(SessionManager session)
        {
            this.session = session;
        }

        public SessionManager Session
        {
            get
            {
                return session;
            }
        }

        public String Server
        {
            get
            {
                return server;
            }
            set
            {
                server = value;
            }

        }
    }
}

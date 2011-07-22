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

namespace Electronic_MIS
{
    

    public partial class LoginTab : UserControl 
    {
        public delegate void LoginEventHandler(object sender, LoginEventArgs e);
        public event LoginEventHandler LoginEvent;        
        
        public SessionManager sessionManager;
        
        protected virtual void OnLoginEvent(LoginEventArgs e)
        {
            if(LoginEvent != null)
            LoginEvent(this, e);
        }

        public LoginTab()
        {
            InitializeComponent();
            sessionManager = new SessionManager();
        }
        
        private void btnLogin_Click(object sender, EventArgs e)
        {
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

            //Build the Connection String
            UriBuilder ub = new UriBuilder();

            string pass = CalculateMD5Hash(txtPassword.Text);
            StringBuilder data = new StringBuilder();
            data.Append("u=" + WebUtility.HtmlEncode(txtUser.Text));
            data.Append("&p=" + WebUtility.HtmlEncode(pass));

            ub.Host = "robertdiazisabitch.dyndns.org/EMIS/Authenticate.php";
            ub.Query = data.ToString();

            //Create the request
            Uri requestUri = ub.Uri;
            WebRequest request = WebRequest.Create(requestUri);
            request.Method = "GET";

            try
            {
                WebResponse response = request.GetResponse();
                
                XmlTextReader xmlReader = new XmlTextReader(response.GetResponseStream());

                while (xmlReader.Read())
                {
                    switch (xmlReader.NodeType)
                    {
                        case XmlNodeType.Element:
                            if (xmlReader.Name == "result")
                            {
                                if (xmlReader.ReadElementContentAsInt() == 0)
                                {
                                    MessageBox.Show("Login failure.  Please check your info and try again.");
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

            LoginEventArgs eventArgs = new LoginEventArgs(sessionManager);
            OnLoginEvent(eventArgs);
        }

        public string CalculateMD5Hash(string input)
        {
            // step 1, calculate MD5 hash from input
            MD5 md5 = System.Security.Cryptography.MD5.Create();
            byte[] inputBytes = System.Text.Encoding.ASCII.GetBytes(input);
            byte[] hash = md5.ComputeHash(inputBytes);

            // step 2, convert byte array to hex string
            StringBuilder sb = new StringBuilder();
            for (int i = 0; i < hash.Length; i++)
            {
                sb.Append(hash[i].ToString("x2"));
            }
            return sb.ToString();
        }        
    }

    public class LoginEventArgs : EventArgs
    {
        SessionManager session;

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
    }
}

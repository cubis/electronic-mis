using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.Security;
using System.Diagnostics;
using System.IO;
using System.Xml;

namespace Electronic_MIS
{
    public partial class PatientInfoTab : UserControl
    {


        SessionManager sessionManager;
        List<Patient> patients;
        string server;

        public PatientInfoTab(SessionManager manager, string activeServer)
        {
            InitializeComponent();
            this.sessionManager = manager;
            patients = new List<Patient>();
            server = activeServer;
        }

        private void label1_Click(object sender, EventArgs e)
        {

        }

        private void Insuranceid_Click(object sender, EventArgs e)
        {

        }

        private void textBox1_TextChanged(object sender, EventArgs e)
        {

        }

        private void PatientInfoTab_Load(object sender, EventArgs e)
        {
            StringBuilder data = new StringBuilder();
            data.Append(server);
            data.Append("viewPatientREST.php");
            data.Append("?u=" + WebUtility.HtmlEncode(sessionManager.User));
            data.Append("&key=" + WebUtility.HtmlEncode(sessionManager.Key));
            data.Append("&pat=all");

            //Create the request


            string url = data.ToString();
            WebRequest request = WebRequest.Create(url);
            request.Method = "GET";

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

                //testing to put input inside textbox1, first name textbox
                //textBox1.Text = "juan";

                while (xmlReader.Read())
                {
                    switch (xmlReader.NodeType)
                    {
                        case XmlNodeType.Element:
                            if (xmlReader.Name == "Patient")
                            {
                                Patient newpat = new Patient();
                                xmlReader.Read();
                                while(xmlReader.Name != "Patient")
                                {                                    
                                    xmlReader.Read();
                                    switch (xmlReader.Name)
                                    {
                                        case "FirstName":
                                            newpat.FirstName = xmlReader.ReadElementContentAsString();
                                            textBox1.Text = newpat.FirstName;
                                            break;
                                        case "ERROR":
                                            MessageBox.Show(xmlReader.ReadElementContentAsString());
                                            break;
                                    }
                                }
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


        }
    }

    class Patient
    {
        String firstn;

        public String FirstName
        {
            get
            {
                return firstn;
            }
            set
            {
                firstn = value;
            }

        }
    }

}

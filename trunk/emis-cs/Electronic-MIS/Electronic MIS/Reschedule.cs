using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.Xml;
using System.IO;
using System.Net.Security;
using System.Security.Cryptography.X509Certificates;
using System.Diagnostics;

namespace Electronic_MIS
{
    public partial class Reschedule : Form
    {
        public DateTime newTime;
        public Doctor doctor;
        List<Doctor> doctors = new List<Doctor>();
        string server;
        SessionManager sessionManager;

        public Reschedule(string Server, SessionManager session)
        {
            InitializeComponent();

            server = Server;
            sessionManager = session;

            dtDate.Value = DateTime.Today;

            DateTime timeEntry = DateTime.Parse("7:00");
            while (timeEntry.Hour < 19)
            {
                cmbTimes.Items.Add(timeEntry.ToShortTimeString());
                timeEntry = timeEntry.AddMinutes(15);
            }

            StringBuilder data = new StringBuilder();
            data.Append(server);
            data.Append("doctorListREST.php");
            data.Append("?u=" + WebUtility.HtmlEncode(sessionManager.UserName.ToString()));
            data.Append("&key=" + WebUtility.HtmlEncode(sessionManager.Key));

            string url = data.ToString();
            Debug.WriteLine(url);
            
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "GET";

            Debug.WriteLine(request.ToString());


            request.Timeout = 50000;

            try
            {
                WebResponse response = request.GetResponse();

                XmlTextReader xmlReader = new XmlTextReader(response.GetResponseStream());

                while (xmlReader.Read())
                {
                    switch (xmlReader.NodeType)
                    {
                        case XmlNodeType.Element:
                            if (xmlReader.Name == "Names")
                            {
                                Doctor doc = new Doctor();
                                xmlReader.Read();
                                while (xmlReader.Name != "Names")
                                {
                                    switch (xmlReader.Name)
                                    {                  

                                        case "DOCID":
                                            doc.DoctorID = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "LastName":
                                            doc.DoctorName = xmlReader.ReadElementContentAsString();
                                            break;

                                        default:
                                            break;
                                    }
                                    xmlReader.Read();
                                }
                                doctors.Add(doc);
                            }
                            break;

                    }
                }

            }
            catch (Exception exp)
            {
                if (exp.Message.Contains("404"))
                {
                    MessageBox.Show("Cannot connect to server.\n  Please try again later.", "Server connection error", MessageBoxButtons.OK);
                }
                else
                {
                    Debug.WriteLine(exp.Message);
                    MessageBox.Show("The program encountered an error.\n  Please try again later.", "Yeah... We didn't plan for this.", MessageBoxButtons.OK);
                }
            }


            foreach (Doctor doc in doctors)
            {
                cmbDocs.Items.Add(doc);
            }
        }

        private void Reschedule_Load(object sender, EventArgs e)
        {            
            
        }

        private void dtTime_ValueChanged(object sender, EventArgs e)
        {

        }

        private List<string> getDoctors()
        {
            List<string> docs = new List<string>();

            //TODO Get list of all Dr's from server

            return docs;

        }

        private void btnOk_Click(object sender, EventArgs e)
        {
            if (dtDate.Value == null || cmbTimes.SelectedItem == null || cmbDocs.SelectedItem == null)
            {
                MessageBox.Show("Please fill out all fields", "Error", MessageBoxButtons.OK);
                return;
            }

            newTime = dtDate.Value;

            //string time = cmbTimes.SelectedItem.ToString().Replace(" AM", "");
            //time = time.Replace(" PM", "");
            DateTime timeObj;
            timeObj = DateTime.Parse(cmbTimes.SelectedItem.ToString());

            newTime = newTime.AddHours(timeObj.TimeOfDay.Hours);
            newTime = newTime.AddMinutes(timeObj.TimeOfDay.Minutes);

            doctor = (Doctor)cmbDocs.SelectedItem;
            DialogResult = System.Windows.Forms.DialogResult.OK;
            Close();
        }

        private void btnCancel_Click(object sender, EventArgs e)
        {
            newTime = new DateTime();
            doctor = null;
            DialogResult = System.Windows.Forms.DialogResult.Cancel;
            Close();
        }
    }
}

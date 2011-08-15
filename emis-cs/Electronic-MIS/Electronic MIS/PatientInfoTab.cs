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
        string doc;
        string docnum;

        public PatientInfoTab(SessionManager manager, string activeServer)
        {
            InitializeComponent();
            this.sessionManager = manager;
            patients = new List<Patient>();
            server = activeServer;
        }

        private void PatientInfoTab_Load(object sender, EventArgs e)
        {
            
            StringBuilder data = new StringBuilder();
            data.Append(server);
            data.Append("viewPatientREST.php");
            data.Append("?u=" + WebUtility.HtmlEncode(sessionManager.UserName));
            data.Append("&key=" + WebUtility.HtmlEncode(sessionManager.Key));
            data.Append("&pat=all");

            //Create the request

            string url = data.ToString();
            WebRequest request = WebRequest.Create(url);
            request.Method = "GET";
            string docID = getdocid();

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
                            if(xmlReader.Name == "ERROR")
                            {
                                MessageBox.Show(xmlReader.ReadElementContentAsString());
                            }
             
                            if (xmlReader.Name == "Patient")
                            {
                                Patient newpat = new Patient();
                                xmlReader.Read();
                                while(xmlReader.Name != "Patient")
                                {
                                    //Patient newpat = new Patient();
                                    xmlReader.Read();
                                    switch (xmlReader.Name)
                                    {
                                        case "FirstName":
                                            newpat.FirstName = xmlReader.ReadElementContentAsString();
                                            textBox1.Text = newpat.FirstName;
                                            break;
                                        case "LastName":
                                            newpat.LastName = xmlReader.ReadElementContentAsString();
                                            textBox2.Text = newpat.LastName;
                                            break;
                                        case "Sex":
                                            newpat.Sex = xmlReader.ReadElementContentAsString();
                                            textBox3.Text = newpat.Sex;
                                            break;
                                        case "Birthday":
                                            newpat.Birthday = xmlReader.ReadElementContentAsString();
                                            textBox5.Text = newpat.Birthday;
                                            break;
                                        case "SSN":
                                            newpat.SSN = xmlReader.ReadElementContentAsString();
                                            textBox6.Text = newpat.SSN;
                                            break;
                                        case "Email":
                                            newpat.Email = xmlReader.ReadElementContentAsString();
                                            textBox7.Text = newpat.Email;
                                            break;
                                        case "PhoneNumber":
                                            newpat.phone = xmlReader.ReadElementContentAsString();
                                            textBox8.Text = newpat.phone;
                                            break;
                                        case "CompanyName":
                                            newpat.Company = xmlReader.ReadElementContentAsString();
                                            textBox9.Text = newpat.Company;
                                            break;
                                        case "PlanType":
                                            newpat.PlanType = xmlReader.ReadElementContentAsString();
                                            textBox10.Text = newpat.PlanType;
                                            break;
                                        case "PlanNum":
                                            newpat.PlanNum = xmlReader.ReadElementContentAsString();
                                            textBox11.Text = newpat.PlanNum;
                                            break;
                                        case "CoveragePercent":
                                            newpat.CoveragePercent = xmlReader.ReadElementContentAsString();
                                            textBox12.Text = newpat.CoveragePercent;
                                            break;
                                        case "CoPay":
                                            newpat.CoPay = xmlReader.ReadElementContentAsString();
                                            textBox13.Text = newpat.CoPay;
                                            break;
                                        case "CoverageStart":
                                            newpat.CoverageStart = xmlReader.ReadElementContentAsString();
                                            textBox14.Text = newpat.CoverageStart;
                                            break;
                                        case "CoverageEnd":
                                            newpat.CoverageEnd = xmlReader.ReadElementContentAsString();
                                            textBox15.Text = newpat.CoverageEnd;
                                            break;
                                        case "FKDoctorID":
                                            doc = xmlReader.ReadElementContentAsString(); 
                                            break;
                                        case "PK_DoctorID":
                                            docnum = xmlReader.ReadElementContentAsString();
                                            break;
                                        case "ERROR":
                                            MessageBox.Show(xmlReader.ReadElementContentAsString());
                                            break;
                                        
                                    }
                                }

                                if (doc == docID)
                                {
                                    patients.Add(newpat);
                                }

                            }
                            //adding a patient from doc to the patients list
                            //have to find a way to figure out which doctor is logged on to be able
                            //to put their certain patients on the combo

                            //hiding the patientbox if user is logged on as a patient
                            int sessionid = sessionManager.UserPermissionLevel;
                            if (sessionid == 1)
                            {
                                patientbox.Hide();
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

            int num = 0;
            foreach (Patient pat in patients)
            {
                    patientbox.Items.Add(pat);
                    Console.WriteLine(num++);
            }         
            
        }


        private void loadPatient(Patient patient)
        {
           patientbox.Items.Add(patient);
        }

        //combobox
        private void patientbox_SelectedIndexChanged(object sender, EventArgs e)
        {
            refreshLabels((Patient)patientbox.SelectedItem);
        }

        private string getdocid()
        {
            string docname = sessionManager.LastName;
            List<Doctor> doctors = new List<Doctor>(); 
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
                if (doc.DoctorName == docname)
                {
                    return doc.DoctorID;
                }
            }

            return "";
        
        }

        private void refreshLabels(Patient patient)
        {
            // fill out the forms with patient information
            // when a certain patient is clicked on in the dropbox
         
            this.textBox1.Text = (patient.FirstName);
            this.textBox2.Text = (patient.LastName);
            this.textBox3.Text = (patient.Sex);
            this.textBox5.Text = (patient.Birthday);
            this.textBox6.Text = (patient.SSN);
            this.textBox7.Text = (patient.Email);
            this.textBox8.Text = (patient.phone);
            this.textBox9.Text = (patient.Company);
            this.textBox10.Text = (patient.PlanType);
            this.textBox11.Text = (patient.PlanNum);
            this.textBox12.Text = (patient.CoveragePercent);
            this.textBox13.Text = (patient.CoPay);
            this.textBox14.Text = (patient.CoverageStart);
            this.textBox15.Text = (patient.CoverageEnd);
        }

    } 
}

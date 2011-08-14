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
                            if(xmlReader.Name == "ERROR")
                            {
                                MessageBox.Show(xmlReader.ReadElementContentAsString());
                            }
                            if (xmlReader.Name == "Patient")
                            {
                                xmlReader.Read();
                                while(xmlReader.Name != "Patient")
                                {
                                    Patient newpat = new Patient();
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
                                        case "ERROR":
                                            MessageBox.Show(xmlReader.ReadElementContentAsString());
                                            break;
                                    }

                                    //adding a new patient to the patients list
                                    patients.Add(newpat);

                                    //checking if the patient has docj as their doctor
                                    //add that patient to the combobox
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

            //patients.Sort();

            foreach (Patient pat in patients)
            {
                patientbox.Items.Add(pat);
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

        private void refreshLabels(Patient patient)
        {

        }

        

        

    }

    class Patient
    {
        String firstn;
        String lastn;
        String sex;
        String bday;
        String ssn;
        String email;
        String phonenum;
        String compname;
        String planType;
        String planNum;
        String covgPerc;
        String copay;
        String covgstart;
        String covgend;


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

        public String LastName
        {
            get
            {
                return lastn;
            }
            set
            {
                lastn = value;
            }
        }

        public String Sex
        {
            get
            {
                return sex;
            }
            set
            {
                sex = value;
            }
        }

        public String Birthday
        {
            get
            {
                return bday;
            }
            set
            {
                bday = value;
            }
        }

        public String SSN
        {
            get
            {
                return ssn;
            }
            set
            {
                ssn = value;
            }
        }

        public String Email
        {
            get
            {
                return email;
            }
            set
            {
                email = value;
            }
        }

        public String phone
        {
            get
            {
                return phonenum;
            }
            set
            {
                phonenum = value;
            }
        }

        public String Company
        {
            get
            {
                return compname;
            }
            set
            {
                compname = value;
            }
        }

        public String PlanType
        {
            get
            {
                return planType;
            }
            set
            {
                planType = value;
            }
        }

        public String PlanNum
        {
            get
            {
                return planNum;
            }
            set
            {
                planNum = value;
            }
        }

        public String CoveragePercent
        {
            get
            {
                return covgPerc;
            }
            set
            {
                covgPerc = value;
            }
        }

        public String CoPay
        {
            get
            {
                return copay;
            }
            set
            {
                copay = value;
            }
        }

        public String CoverageStart
        {
            get
            {
                return covgstart;
            }
            set
            {
                covgstart = value;
            }
        }

        public String CoverageEnd
        {
            get
            {
                return covgend;
            }
            set
            {
                covgend = value;
            }
        }

        public override string ToString()
        {
            return FirstName + LastName;
        }
    }

}

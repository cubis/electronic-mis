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

                while (xmlReader.Read())
                {
                    switch (xmlReader.NodeType)
                    {
                        case XmlNodeType.Element:
                            if(xmlReader.Name == "ERROR")
                            {
                                MessageBox.Show(xmlReader.ReadElementContentAsString());
                            }
                            Patient newpat = new Patient();
                            if (xmlReader.Name == "Patient")
                            {
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
                                        case "ERROR":
                                            MessageBox.Show(xmlReader.ReadElementContentAsString());
                                            break;
                                    }
                                }
                                patients.Add(newpat);
                                int numberdoc = sessionManager.UserID;
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


                            if (doc == "1")
                            {
                                sessionid = sessionManager.UserID;
                                patients.Add(newpat); 
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
        String doctornum;


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

        public string Doctor
        {
            get
            {
                return doctornum;
            }
            set
            {
                doctornum = value;
            }
        }



        public override string ToString()
        {
            return FirstName + " " + LastName + "\n";
        }
    }

}

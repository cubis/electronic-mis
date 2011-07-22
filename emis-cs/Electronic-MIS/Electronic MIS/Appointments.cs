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


/*
 * <appt>
 *  <appID>1234567</appID>
 *  <date>7/11/2012</date>
 *  <time>09:45</time>
 *  <doctor>Gonzalez</doctor>
 *  <reason>I gots the herps</reason>
 *  <remind>true</remind>
 * </appt>
 * 
 * */



namespace Electronic_MIS
{
    public partial class Appointments : Form
    {
        SessionManager sessionManager;
        List<Appt> appointments;

        public Appointments(SessionManager session)
        {
            InitializeComponent();
            sessionManager = session;
            appointments = new List<Appt>();
        }

        private void Appointments_Load(object sender, EventArgs e)
        {


            listBox1.Items.Add(DateTime.Today.ToLongDateString() + " " + DateTime.Today.ToLongTimeString());            
        }

        private void listBox1_SelectedIndexChanged(object sender, EventArgs e)
        {
            calAppointments.SetDate(DateTime.Parse(listBox1.SelectedValue.ToString()));
        }

        private void listBox1_DoubleClick(object sender, EventArgs e)
        {
            calAppointments.SetDate(DateTime.Parse(listBox1.SelectedItem.ToString()));
        }

        private void calAppointments_DateSelected(object sender, DateRangeEventArgs e)
        {
            if (true)
            {
               DialogResult result = MessageBox.Show("Appointment time at ...", "Appointment", MessageBoxButtons.OKCancel, MessageBoxIcon.Information);
               if (result == System.Windows.Forms.DialogResult.Cancel)
               {
                   result = MessageBox.Show("Are you sure you want to cancel this?", "Cancel app",MessageBoxButtons.YesNo,MessageBoxIcon.Warning);
                   if (result == System.Windows.Forms.DialogResult.Yes)
                   {
                       //Cancel appt.
                   }
               }
            }
        }

        private void logoutToolStripMenuItem_Click(object sender, EventArgs e)
        {
            this.Close();
        }

        private void exitToolStripMenuItem_Click(object sender, EventArgs e)
        {
            Application.Exit();
        }

        private void getAppointments()
        {
            //Build the Connection String
            UriBuilder ub = new UriBuilder();

            StringBuilder data = new StringBuilder();
            data.Append("u=" + WebUtility.HtmlEncode(sessionManager.User));
            data.Append("&key=" + WebUtility.HtmlEncode(sessionManager.Key));

            ub.Host = "67.10.181.224/~cookie/emis-dev/Appointments.php";
            ub.Query = data.ToString();

            //Create the request
            Uri requestUri = ub.Uri;
            WebRequest request = WebRequest.Create(requestUri);
            request.Method = "GET";

            try
            {
                WebResponse response = request.GetResponse();

                StreamReader reader = new StreamReader(response.GetResponseStream());
                XmlTextReader xmlReader = new XmlTextReader(response.GetResponseStream());
                while (xmlReader.Read())
                {
                    switch (xmlReader.NodeType)
                    {
                        case XmlNodeType.Element:
                            if (xmlReader.Name == "appt")
                            {
                                Appt newAppt = new Appt();
                                xmlReader.Read();
                                switch (xmlReader.Name)
                                {
                                    case "date":
                                        newAppt.AppointmentTime = xmlReader.ReadElementContentAsDateTime();
                                        break;
                                    case "time":
                                        DateTime time = xmlReader.ReadElementContentAsDateTime();
                                        newAppt.AppointmentTime.AddHours(time.Hour);
                                        newAppt.AppointmentTime.AddMinutes(time.Minute);
                                        break;

                                    default:
                                        break;
                                }

                                appointments.Add(newAppt);
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

    class Appt
    {
        String doc;
        String reason;
        int apptID;
        bool remind;
        DateTime appTime;


        public String Doctor
        {
            get
            {
                return doc;
            }
            set
            {
                doc = value;
            }

        }

        public String Reason
        {
            get
            {
                return reason;
            }
            set
            {
                reason = value;
            }

        }

        public int AppointmentID
        {
            get
            {
                return apptID;
            }
            set
            {
                apptID = value;
            }

        }

        public bool Remind
        {
            get
            {
                return remind;
            }
            set
            {
                remind = value;
            }

        }

        public DateTime AppointmentTime
        {
            get
            {
                return appTime;
            }
            set
            {
                appTime = value;
            }

        }
    }
}

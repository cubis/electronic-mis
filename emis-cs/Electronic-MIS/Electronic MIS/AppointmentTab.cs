using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.Xml;
using System.IO;

namespace Electronic_MIS
{
    public partial class AppointmentTab : UserControl
    {
        SessionManager sessionManager;
        List<Appt> appointments;

        public AppointmentTab(SessionManager session)
        {
            InitializeComponent();
            sessionManager = session;
            appointments = new List<Appt>();
        }

        private void AppointmentTab_Load(object sender, EventArgs e)
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
                    result = MessageBox.Show("Are you sure you want to cancel this?", "Cancel app", MessageBoxButtons.YesNo, MessageBoxIcon.Warning);
                    if (result == System.Windows.Forms.DialogResult.Yes)
                    {
                        //Cancel appt.
                    }
                }
            }
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
                MessageBox.Show(exp.Message, "Yeah... We didn't plan for this.", MessageBoxButtons.OK);
                Application.Exit();
            }
        }

        private void checkBox1_CheckedChanged(object sender, EventArgs e)
        {

        }

        private void button1_Click(object sender, EventArgs e)
        {

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
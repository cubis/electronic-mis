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
using System.Net.Security;
using System.Security.Cryptography.X509Certificates;
using System.Diagnostics;

namespace Electronic_MIS
{
    public partial class AppointmentTab : UserControl
    {
        SessionManager sessionManager;
        List<Appointment> appointments;
        string server;

        public AppointmentTab(SessionManager session,string activeServer)
        {
            InitializeComponent();
            sessionManager = session;
            server = activeServer;
            appointments = new List<Appointment>();
        }

        private void AppointmentTab_Load(object sender, EventArgs e)
        {
            Cursor.Current = Cursors.WaitCursor;

            getAppointments();

            appointments.Sort();

            foreach (Appointment appt in appointments)
            {
                calAppointments.AddBoldedDate(appt.AppointmentTime);
                appointmentListBox.Items.Add(appt);
            }

            calAppointments.UpdateBoldedDates();

            Cursor.Current = Cursors.Arrow;
        }

        private void listBox1_SelectedIndexChanged(object sender, EventArgs e)
        {
            calAppointments.SetDate(DateTime.Parse(appointmentListBox.SelectedValue.ToString()));
        }

        private void listBox1_DoubleClick(object sender, EventArgs e)
        {
            calAppointments.SetDate(DateTime.Parse(appointmentListBox.SelectedItem.ToString()));
        }

        private void calAppointments_DateSelected(object sender, DateRangeEventArgs e)
        {
            selectAppointmentByDate(e.End);
        }

        private void exitToolStripMenuItem_Click(object sender, EventArgs e)
        {
            Application.Exit();
        }

        private void getAppointments()
        {
            StringBuilder data = new StringBuilder();
            data.Append(server);
            data.Append("viewPatApptsREST.php");
            data.Append("?u=" + WebUtility.HtmlEncode(sessionManager.User));
            data.Append("&key=" + WebUtility.HtmlEncode(sessionManager.Key));

            string url = data.ToString();
            Debug.WriteLine(url);

            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
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
                            if (xmlReader.Name == "Appointment")
                            {
                                Appointment newAppt = new Appointment();
                                StringBuilder sb = new StringBuilder();
                                xmlReader.Read();
                                while (xmlReader.Name != "Appointment")
                                {
                                    xmlReader.Read();
                                    switch (xmlReader.Name)
                                    {
                                        case "DATE":
                                            sb.Append(xmlReader.ReadElementContentAsString() + " ");
                                            break;
                                        
                                        case "TIME":
                                            sb.Append(xmlReader.ReadElementContentAsString());
                                            break;
                                        
                                        case "doctor":
                                            newAppt.Doctor = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "REASON":
                                            newAppt.Reason = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "remind":
                                            newAppt.Remind = xmlReader.ReadElementContentAsBoolean();
                                            break;

                                        default:
                                            break;
                                    }
                                }
                                newAppt.AppointmentTime = DateTime.Parse(sb.ToString());
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
        }

        private void checkBox1_CheckedChanged(object sender, EventArgs e)
        {

        }

        private void appointmentListBox_SelectedIndexChanged(object sender, EventArgs e)
        {
            Appointment selectedAppointment = (Appointment)appointmentListBox.SelectedItem;
            if (selectedAppointment != null)
            {
                calAppointments.SetDate(selectedAppointment.AppointmentTime);
            }
            selectAppointment();
        }

        private void calAppointments_DateChanged(object sender, DateRangeEventArgs e)
        {
            selectAppointmentByDate(e.End);
        }

        private void selectAppointmentByDate(DateTime date)
        {
            comboBox1.Items.Clear();
            comboBox1.ResetText();
            textBox1.Clear();
            remindMeChkBox.Visible = false;
            CancelButton.Visible = false;

            foreach (Appointment appt in appointments)
            {
                if (appt.AppointmentTime.Date == date.Date)
                {
                    comboBox1.Items.Add(appt);
                }
            }

            if (comboBox1.Items.Count > 0)
            {
                comboBox1.SelectedIndex = 0;
                selectAppointment();
            }


        }

        private void comboBox1_SelectedIndexChanged(object sender, EventArgs e)
        {
            selectAppointment();
        }

        private void selectAppointment()
        {
            Appointment appt = (Appointment)comboBox1.SelectedItem;

            calAppointments.SetDate(appt.AppointmentTime);
            appointmentListBox.SelectedItem = appt;

            StringBuilder sb = new StringBuilder();
            sb.Append(appt.AppointmentTime.ToLongDateString());
            sb.Append(" ");
            sb.AppendLine(appt.AppointmentTime.ToLongTimeString());
            sb.AppendLine("Appointment with Dr. " + appt.Doctor);
            sb.AppendLine("Reason: \n\t" + appt.Reason);

            textBox1.Text = sb.ToString();

            if (appt.Remind)
            {
                remindMeChkBox.CheckState = CheckState.Checked;
            }

            CancelButton.Visible = true;
            remindMeChkBox.Visible = true;
        }

        private void CancelButton_Click(object sender, EventArgs e)
        {
            if (DialogResult.Yes == MessageBox.Show("Are you sure you want to cancel this appointment?",
                "Confirm Cancel", MessageBoxButtons.YesNo))
            {
                removeAppointment();
            }
        }

        private void removeAppointment()
        {
            Appointment appt = (Appointment)comboBox1.SelectedItem;

            appointments.Remove(appt);
            calAppointments.RemoveBoldedDate(appt.AppointmentTime);
            appointmentListBox.Items.Remove(appt);
            comboBox1.Items.Remove(appt);

            if (comboBox1.Items.Count > 1)
            {
                comboBox1.SelectedIndex = 0;
                selectAppointment();
            }
            else
            {
                comboBox1.SelectedIndex = -1;
            }

            textBox1.Clear();
            comboBox1.Text = "";

            CancelButton.Visible = false;
            remindMeChkBox.Visible = false;

            calAppointments.UpdateBoldedDates();
        }

        public static void SetCertificatePolicy()
        {
            ServicePointManager.ServerCertificateValidationCallback = RemoteCertificateValidate;
        }

        /// <summary>
        /// Remotes the certificate validate./// 
        /// </summary>
        private static bool RemoteCertificateValidate(object sender, X509Certificate cert, X509Chain chain, SslPolicyErrors error)
        {
            // trust any certificate!!!    
            System.Console.WriteLine("Warning, trust any certificate");
            return true;
        }
  
    }

    class Appointment : IComparable
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

        public override string ToString()
        {
            return (AppointmentTime.ToLongDateString() + ", " +AppointmentTime.ToLongTimeString());
        }

        public int CompareTo(object obj)
        {
            Appointment comp = (Appointment)obj;

            return DateTime.Compare(this.AppointmentTime,comp.AppointmentTime);
        }
    }
}
﻿using System;
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
        List<Appointment> appointments;

        public AppointmentTab(SessionManager session)
        {
            InitializeComponent();
            sessionManager = session;
            appointments = new List<Appointment>();
        }

        private void AppointmentTab_Load(object sender, EventArgs e)
        {
            getAppointments();

            appointments.Sort();

            foreach (Appointment appt in appointments)
            {
                calAppointments.AddBoldedDate(appt.AppointmentTime);
                appointmentListBox.Items.Add(appt);
            }

            calAppointments.UpdateBoldedDates();
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
            //Build the Connection String
            UriBuilder ub = new UriBuilder();

            StringBuilder data = new StringBuilder();
            data.Append("u=" + WebUtility.HtmlEncode(sessionManager.User));
            data.Append("&key=" + WebUtility.HtmlEncode(sessionManager.Key));

            ub.Host = "robertdiazisabitch.dyndns.org/EMIS/appointmentsREST.php";
            ub.Query = data.ToString();

            //Create the request
            Uri requestUri = ub.Uri;
            WebRequest request = WebRequest.Create(requestUri);
            request.Method = "GET";

            try
            {
                //WebResponse response = request.GetResponse();
                TextReader respone = new StringReader(Properties.Resources.AppointmentXMLSample);
                XmlTextReader xmlReader = new XmlTextReader(respone);
                while (xmlReader.Read())
                {
                    switch (xmlReader.NodeType)
                    {
                        case XmlNodeType.Element:
                            if (xmlReader.Name == "appointment")
                            {
                                Appointment newAppt = new Appointment();
                                StringBuilder sb = new StringBuilder();
                                xmlReader.Read();
                                while (xmlReader.Name != "appointment")
                                {
                                    xmlReader.Read();
                                    switch (xmlReader.Name)
                                    {
                                        case "date":
                                            sb.Append(xmlReader.ReadElementContentAsString() + " ");
                                            break;
                                        
                                        case "time":
                                            sb.Append(xmlReader.ReadElementContentAsString());
                                            break;
                                        
                                        case "doctor":
                                            newAppt.Doctor = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "reason":
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
                MessageBox.Show(exp.Message, "Yeah... We didn't plan for this.", MessageBoxButtons.OK);
                Application.Exit();
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
            }

            selectAppointment();
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
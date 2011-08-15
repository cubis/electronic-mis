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
        public delegate void PrintEventHandler(object sender, PrintEventArgs e);
        public event PrintEventHandler PrintEvent;        
    

        SessionManager sessionManager;
        List<Appointment> appointments;
        string server;
        Appointment selectedAppointment;

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

        /* Example incoming data 
         * -<Appointment>
         * <APPTID>9</APPTID> 
         * <PatID>3</PatID> 
         * <DocID>1</DocID> <
         * DocName>J</DocName> 
         * <PatFirstName>Basil</PatFirstName> 
         * <PatLastName>Sattler</PatLastName> 
         * <REASON>Getting out of work sutff 
         * </REASON> <DATE>2011-07-27</DATE> 
         * <TIME>09:45:00</TIME> 
         * <STATUS>Scheduled</STATUS> 
         * <REMINDER>0</REMINDER> 
         * </Appointment>
         */
        private void getAppointments()
        {
            StringBuilder data = new StringBuilder();
            data.Append(server);
            data.Append("apptViewREST.php");
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
                                    switch (xmlReader.Name)
                                    {
                                        case "APPTID":
                                            newAppt.AppointmentID = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "PatID":
                                            newAppt.PatientID = xmlReader.ReadElementContentAsInt();
                                            break;

                                        case "DATE":
                                            sb.Append(xmlReader.ReadElementContentAsString() + " ");
                                            break;
                                        
                                        case "TIME":
                                            sb.Append(xmlReader.ReadElementContentAsString());
                                            break;

                                        case "PatFirstName":
                                            newAppt.PatientFirstName = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "PatLastName":
                                            newAppt.PatientLastName = xmlReader.ReadElementContentAsString();
                                            break;                                                
                                            
                                        case "DocID":
                                            newAppt.Doctor.DoctorID = xmlReader.ReadElementContentAsString();
                                            break;
                                        
                                        case "DocName":
                                            newAppt.Doctor.DoctorName = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "REASON":
                                            newAppt.Reason = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "STATUS": ;
                                            newAppt.Status = xmlReader.ReadElementContentAsString();
                                            break;

                                        case "REMINDER":
                                            newAppt.Remind = int.Parse(xmlReader.ReadElementContentAsString());
                                            break;

                                        default:
                                            break;
                                    }
                                    xmlReader.Read();
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

        private void remindMeChkBox_CheckedChanged(object sender, EventArgs e)
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
            cmbAppointments.Items.Clear();
            cmbAppointments.ResetText();
            textBox1.Clear();
            remindMeChkBox.Visible = false;
            CancelButton.Visible = false;
            btnReschedule.Visible = false;
            btnReciept.Visible = false;

            foreach (Appointment appt in appointments)
            {
                if (appt.AppointmentTime.Date == date.Date)
                {
                    cmbAppointments.Items.Add(appt);
                }
            }

            if (cmbAppointments.Items.Count > 0)
            {
                cmbAppointments.SelectedIndex = 0;
                selectAppointment();
            }


        }

        private void comboBox1_SelectedIndexChanged(object sender, EventArgs e)
        {
            selectAppointment();
        }

        private void selectAppointment()
        {
            Appointment appt = (Appointment)cmbAppointments.SelectedItem;
            selectedAppointment = appt;

            calAppointments.SetDate(appt.AppointmentTime);
            appointmentListBox.SelectedItem = appt;

            StringBuilder sb = new StringBuilder();
            sb.Append(appt.AppointmentTime.ToLongDateString());
            sb.Append(" ");
            sb.AppendLine(appt.AppointmentTime.ToLongTimeString());
            sb.AppendLine("Appointment with Dr. " + appt.Doctor);
            sb.AppendLine("Reason: \n\t" + appt.Reason);
            sb.AppendLine("Appointment Status:\t" + appt.Status);

            textBox1.Text = sb.ToString();

            if (appt.Remind != 0)
            {
                remindMeChkBox.CheckState = CheckState.Checked;
            }

            if (appt.AppointmentTime.CompareTo(DateTime.Today) > 0)
            {
                CancelButton.Visible = true;
                btnReschedule.Visible = true;
                remindMeChkBox.Visible = true;
            }
            else
            {
                btnReciept.Visible = true;
            }
        }

        private void CancelButton_Click(object sender, EventArgs e)
        {
            if (DialogResult.Yes == MessageBox.Show("Are you sure you want to cancel this appointment?",
                "Confirm Cancel", MessageBoxButtons.YesNo))
            {
                selectedAppointment.Status = "Canceled";
            }

            updateAppointment();
            selectAppointment();
        }

        private void removeAppointment()
        {
            Appointment appt = (Appointment)cmbAppointments.SelectedItem;

            appointments.Remove(appt);
            calAppointments.RemoveBoldedDate(appt.AppointmentTime);
            appointmentListBox.Items.Remove(appt);
            cmbAppointments.Items.Remove(appt);

            if (cmbAppointments.Items.Count > 1)
            {
                cmbAppointments.SelectedIndex = 0;
                selectAppointment();
            }
            else
            {
                cmbAppointments.SelectedIndex = -1;
            }

            textBox1.Clear();
            cmbAppointments.Text = "";

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

        private void btnReschedule_Click(object sender, EventArgs e)
        {
            DateTime newApptTime;
            Doctor doctor;
            Reschedule resched = new Reschedule(server,sessionManager);
            if (resched.ShowDialog() == DialogResult.OK)
            {

                newApptTime = resched.newTime;
                doctor = resched.doctor;

                selectedAppointment.AppointmentTime = newApptTime;
                selectedAppointment.Doctor = resched.doctor;
                selectedAppointment.Status = "Scheduled";

                if (selectedAppointment.AppointmentTime.CompareTo(DateTime.Now) > 0)
                {
                    updateAppointment();
                }
                else
                {
                    MessageBox.Show("You must reschedule an appointment for a date later than today.", "Error", MessageBoxButtons.OK);
                }

                refreshTab();
            }
            else
            {

            }
        }

        protected virtual void OnPrintEvent(PrintEventArgs e)
        {
            if (PrintEvent != null)
                PrintEvent(this, e);
        }

        private void btnReciept_Click(object sender, EventArgs e)
        {
            PrintEventArgs eventArgs = new PrintEventArgs((Appointment)cmbAppointments.SelectedItem);

            OnPrintEvent(eventArgs);
        }

        private void refreshTab()
        {
            appointments.Clear();
            appointmentListBox.Items.Clear();
            calAppointments.RemoveAllBoldedDates();
            cmbAppointments.Items.Clear();
            cmbAppointments.ResetText();
            textBox1.Clear();
            remindMeChkBox.Visible = false;
            CancelButton.Visible = false;
            btnReciept.Visible = false;
            btnReschedule.Visible = false;
            
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

        private void updateAppointment()
        {

            StringBuilder url = new StringBuilder();
            url.Append(server);
            url.Append("editApptREST.php?");

            StringBuilder data = new StringBuilder();
            data.Append("u=" + WebUtility.HtmlEncode(sessionManager.UserName));
            data.Append("&key=" + WebUtility.HtmlEncode(sessionManager.Key));
            data.Append("&aid=" + WebUtility.HtmlEncode(selectedAppointment.AppointmentID));
            data.Append("&status=" + WebUtility.HtmlEncode(selectedAppointment.Status));
            String reason = selectedAppointment.Reason.Replace(" ", "%20");
            reason = reason.Replace("?", "%3f");
            reason = reason.Replace("\n", "");
            reason = reason.Replace("\t", "");
            data.Append("&reason=" + WebUtility.HtmlEncode(reason));
            StringBuilder timeString = new StringBuilder();
            if (selectedAppointment.AppointmentTime.Hour < 10)
            {
                timeString.Append("0");
            }
            timeString.Append(selectedAppointment.AppointmentTime.Hour);
            timeString.Append(":");
            if (selectedAppointment.AppointmentTime.Minute < 10)
            {
                timeString.Append("0");
            }
            timeString.Append(selectedAppointment.AppointmentTime.Minute);
            data.Append("&time=" + WebUtility.HtmlEncode(timeString.ToString()));
            string dateFormat = "yyyy-MM-dd";
            string formattedDate = selectedAppointment.AppointmentTime.Date.ToString(dateFormat);
            data.Append("&date=" + WebUtility.HtmlEncode(formattedDate));
            data.Append("&doctor=" + WebUtility.HtmlEncode(selectedAppointment.Doctor.DoctorID));
            data.Append("&patient=" + WebUtility.HtmlEncode(selectedAppointment.PatientID.ToString()));
            data.Append("&reminder=");

            if (selectedAppointment.Remind == 1)
            {
                data.Append("true");
            }
            else
            {
                data.Append("false");
            }

            Debug.WriteLine(url);

            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url.ToString());
            request.ContentLength = Encoding.ASCII.GetByteCount(data.ToString());
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";

            request.Timeout = 50000;

            Cursor.Current = Cursors.WaitCursor;

            Stream requestStream = request.GetRequestStream();
            requestStream.Write(Encoding.ASCII.GetBytes(data.ToString()), 0, Encoding.ASCII.GetByteCount(data.ToString()));
            requestStream.Close();

            WebResponse response = request.GetResponse();

            Debug.WriteLine("Response:");
            StreamReader reader = new StreamReader(response.GetResponseStream());
            Debug.WriteLine(reader.ReadToEnd());

            response.Close();

            Cursor.Current = Cursors.Arrow;
        }

        private void remindMeChkBox_Click(object sender, EventArgs e)
        {
            if (remindMeChkBox.CheckState == CheckState.Checked)
            {
                selectedAppointment.Remind = 1;
            }
            else
            {
                selectedAppointment.Remind = 0;
            }
            updateAppointment();
        }

    }

    public class PrintEventArgs : EventArgs
    {
        Appointment appt;

        public PrintEventArgs(Appointment appointment)
        {
            appt = appointment;
        }

        public Appointment Appointment
        {
            get
            {
                return appt;
            }
        }
    }
}
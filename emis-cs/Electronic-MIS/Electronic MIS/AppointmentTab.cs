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

            request.Timeout = 10000;

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

                                        case "REMIND":
                                            int remind = int.Parse(xmlReader.ReadElementContentAsString());
                                            if (remind == 0)
                                            {
                                                newAppt.Remind = false;
                                            }
                                            else
                                            {
                                                newAppt.Remind = true;
                                            }
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

        private void checkBox1_CheckedChanged(object sender, EventArgs e)
        {
            //TODO: Add in code to change reminder
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
                removeAppointment();
            }
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
            String doctor;
            Reschedule resched = new Reschedule();
            resched.ShowDialog();

            newApptTime = resched.newTime;
            doctor = resched.doctor;


            //TODO:  Add in REST call to change appt. time
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
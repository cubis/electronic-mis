using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace Electronic_MIS
{
    public partial class Appointments : Form
    {
        SessionManager sessionManager;

        public Appointments(SessionManager session)
        {
            InitializeComponent();
            sessionManager = session;
        }

        private void Appointments_Load(object sender, EventArgs e)
        {
         //TODO
            //Add in the code here to read in appointments and fill in the calendar

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
    }
}

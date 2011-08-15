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
    public partial class Reschedule : Form
    {
        public DateTime newTime;
        public Doctor doctor;

        public Reschedule()
        {
            InitializeComponent();

            dtDate.Value = DateTime.Today;

            DateTime timeEntry = DateTime.Parse("7:00");
            while (timeEntry.Hour < 19)
            {
                cmbTimes.Items.Add(timeEntry.ToShortTimeString());
                timeEntry = timeEntry.AddMinutes(15);
            }

            //Add code to get doctor
        }

        private void Reschedule_Load(object sender, EventArgs e)
        {            
            
        }

        private void dtTime_ValueChanged(object sender, EventArgs e)
        {

        }

        private List<string> getDoctors()
        {
            List<string> docs = new List<string>();

            //TODO Get list of all Dr's from server

            return docs;

        }

        private void btnOk_Click(object sender, EventArgs e)
        {
            if (dtDate.Value == null || cmbTimes.SelectedItem == null || cmbDocs.SelectedItem == null)
            {
                MessageBox.Show("Please fill out all fields", "Error", MessageBoxButtons.OK);
                return;
            }

            newTime = dtDate.Value;
            newTime.Add(DateTime.Parse(cmbTimes.SelectedItem.ToString()).TimeOfDay);
            doctor = (Doctor)cmbDocs.SelectedItem;
            Close();
        }

        private void btnCancel_Click(object sender, EventArgs e)
        {
            newTime = new DateTime();
            doctor = null;
            Close();
        }
    }
}

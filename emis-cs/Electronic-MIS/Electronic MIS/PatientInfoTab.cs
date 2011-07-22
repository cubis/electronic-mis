using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace Electronic_MIS
{
    public partial class PatientInfoTab : UserControl
    {
        SessionManager sessionManager;

        public PatientInfoTab(SessionManager manager)
        {
            InitializeComponent();
            this.sessionManager = manager;
        }

        private void label1_Click(object sender, EventArgs e)
        {

        }

        private void Insuranceid_Click(object sender, EventArgs e)
        {

        }
    }
}

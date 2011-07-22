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
    public partial class MainForm : Form
    {

        SessionManager sessionManager;

        public MainForm()
        {
            InitializeComponent();
        }

        private void MainForm_Load(object sender, EventArgs e)
        {
            
            /*
            this.Visible = true;
            while (true)
            {
                Login login = new Login();
                login.ShowDialog(this);
                sessionManager = login.sessionManager;
                login.Dispose();

                Appointments appts = new Appointments(sessionManager);
                appts.ShowDialog(this);

                appts.Dispose();
            }
             * */
        }

        private void exitToolStripMenuItem_Click(object sender, EventArgs e)
        {
            exitClean();
            //this.Close();
        }

        private void exitClean()
        {
            sessionManager.Key = "";
            sessionManager.User = "";
        }

        private void navigationTree_AfterSelect(object sender, TreeViewEventArgs e)
        {
            if (e.Node.Name == "LoginNode")
            {
                tabControl1.TabPages[0].Controls.Add(new LoginTab());
            }
        }
    }

    class loginTab : TabPage
    {
        

    }
}

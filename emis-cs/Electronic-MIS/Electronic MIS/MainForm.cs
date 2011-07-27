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
        string activeServer;

        public MainForm()
        {
            InitializeComponent();
            sessionManager = new SessionManager();
        }

        private void exitToolStripMenuItem_Click(object sender, EventArgs e)
        {
            exitClean();
            Application.Exit();
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
                if(!tabViewer.TabPages.ContainsKey("Login")){

                    tabViewer.TabPages.Add("Login", "Login");
                    int index = tabViewer.TabPages.IndexOfKey("Login");

                    LoginTab login = new LoginTab();
                    login.LoginEvent += new LoginTab.LoginEventHandler(login_LoginEvent);

                    tabViewer.TabPages[index].Controls.Add(login);
                }
                tabViewer.SelectTab("Login");
            }
            else if (e.Node.Name == "PatientInfoNode")
            {
                if (tabViewer.TabPages.ContainsKey("Welcome"))
                {
                    tabViewer.TabPages.RemoveByKey("Welcome");
                }

                if (!tabViewer.TabPages.ContainsKey("PatientInfo"))
                {

                    tabViewer.TabPages.Add("PatientInfo", "PatientInfo");
                    int index = tabViewer.TabPages.IndexOfKey("PatientInfo");

                    PatientInfoTab patientTab = new PatientInfoTab(sessionManager,activeServer);

                    tabViewer.TabPages[index].Controls.Add(patientTab);
                }
                tabViewer.SelectTab("PatientInfo");
            }
            else if (e.Node.Name == "AppointmentNode")
            {
                if (tabViewer.TabPages.ContainsKey("Welcome"))
                {
                    tabViewer.TabPages.RemoveByKey("Welcome");
                }
                if (!tabViewer.TabPages.ContainsKey("Appointments"))
                {

                    tabViewer.TabPages.Add("Appointments", "Appointments");
                    int index = tabViewer.TabPages.IndexOfKey("Appointments");

                    AppointmentTab appTab = new AppointmentTab(sessionManager,activeServer);

                    tabViewer.TabPages[index].Controls.Add(appTab);
                }
                tabViewer.SelectTab("Appointments");
            }
            else if (e.Node.Name == "WelcomeNode")
            {
                if (!tabViewer.TabPages.ContainsKey("Welcome"))
                {

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    int index = tabViewer.TabPages.IndexOfKey("Welcome");

                    WelcomeTab appTab = new WelcomeTab();

                    tabViewer.TabPages[index].Controls.Add(appTab);
                }
                tabViewer.SelectTab("Welcome");
            }
            else if (e.Node.Name == "LogoutNode")
            {

                DialogResult result = MessageBox.Show("Are you sure you want to logout?", "Confirm Logout", MessageBoxButtons.OKCancel);
                if (result == System.Windows.Forms.DialogResult.OK)
                {
                    sessionManager = new SessionManager();
                    sessionManager.User = "";
                    sessionManager.Key = "";
                    tabViewer.TabPages.Clear();

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    int index = tabViewer.TabPages.IndexOfKey("Welcome");

                    WelcomeTab welcomeTab = new WelcomeTab();
                    tabViewer.TabPages[index].Controls.Add(welcomeTab);

                    updateNavTree();
                }
            }
        }

        private void splitContainer1_Panel1_SizeChanged(object sender, EventArgs e)
        {
            navigationTree.Width = splitContainer1.Panel1.Width;
            navigationTree.Height = splitContainer1.Panel1.Height;
        }

        private void splitContainer1_Panel2_SizeChanged(object sender, EventArgs e)
        {
            tabViewer.Height = splitContainer1.Panel2.Height;
            tabViewer.Width = splitContainer1.Panel2.Width;
        }

        private void login_LoginEvent(object sender, LoginEventArgs e)
        {
            sessionManager = e.Session;
            activeServer = e.Server;
            if(sessionManager.IsLoggedIn)
                tabViewer.TabPages.RemoveByKey("Login");
            updateNavTree();
        }

        private void updateNavTree(){
            if (sessionManager.IsLoggedIn)
            {
                navigationTree.Nodes.RemoveByKey("LoginNode");
                navigationTree.Nodes.Add("AppointmentNode", "View Your Appointments");
                navigationTree.Nodes.Add("PatientInfoNode", "View Your Patient Info");
                navigationTree.Nodes.Add("LogoutNode", "Logout");
                navigationTree.SelectedNode = navigationTree.Nodes[0];
            }
            else
            {
                navigationTree.Nodes.Clear();
                navigationTree.Nodes.Add("WelcomeNode", "Welcome");
                navigationTree.Nodes.Add("LoginNode", "Login");
            }


        }

        private void logoutToolStripMenuItem_Click(object sender, EventArgs e)
        {
            if (sessionManager.IsLoggedIn)
            {
                TreeNode node = new TreeNode("Logout");
                node.Name = "LogoutNode";
                TreeViewEventArgs args = new TreeViewEventArgs(node);
                navigationTree_AfterSelect(this, args);
            }
        }

        private void loginToolStripMenuItem_Click(object sender, EventArgs e)
        {
            if (!sessionManager.IsLoggedIn)
            {
                TreeNode node = new TreeNode("Login");
                node.Name = "LoginNode";
                TreeViewEventArgs args = new TreeViewEventArgs(node);
                navigationTree_AfterSelect(this, args);
            }
        }

        private void closeThisTabToolStripMenuItem_Click(object sender, EventArgs e)
        {
            tabViewer.TabPages.Remove(tabViewer.SelectedTab);
        }

        private void navigationTree_NodeMouseClick(object sender, TreeNodeMouseClickEventArgs e)
        {
            if (e.Node.Name == "LoginNode")
            {
                if (!tabViewer.TabPages.ContainsKey("Login"))
                {

                    tabViewer.TabPages.Add("Login", "Login");
                    int index = tabViewer.TabPages.IndexOfKey("Login");

                    LoginTab login = new LoginTab();
                    login.LoginEvent += new LoginTab.LoginEventHandler(login_LoginEvent);

                    tabViewer.TabPages[index].Controls.Add(login);
                }
                tabViewer.SelectTab("Login");
            }
            else if (e.Node.Name == "PatientInfoNode")
            {
                if (tabViewer.TabPages.ContainsKey("Welcome"))
                {
                    tabViewer.TabPages.RemoveByKey("Welcome");
                }

                if (!tabViewer.TabPages.ContainsKey("PatientInfo"))
                {

                    tabViewer.TabPages.Add("PatientInfo", "PatientInfo");
                    int index = tabViewer.TabPages.IndexOfKey("PatientInfo");

                    PatientInfoTab patientTab = new PatientInfoTab(sessionManager, activeServer);

                    tabViewer.TabPages[index].Controls.Add(patientTab);
                }
                tabViewer.SelectTab("PatientInfo");
            }
            else if (e.Node.Name == "AppointmentNode")
            {
                if (tabViewer.TabPages.ContainsKey("Welcome"))
                {
                    tabViewer.TabPages.RemoveByKey("Welcome");
                }
                if (!tabViewer.TabPages.ContainsKey("Appointments"))
                {

                    tabViewer.TabPages.Add("Appointments", "Appointments");
                    int index = tabViewer.TabPages.IndexOfKey("Appointments");

                    AppointmentTab appTab = new AppointmentTab(sessionManager, activeServer);

                    tabViewer.TabPages[index].Controls.Add(appTab);
                }
                tabViewer.SelectTab("Appointments");
            }
            else if (e.Node.Name == "WelcomeNode")
            {
                if (!tabViewer.TabPages.ContainsKey("Welcome"))
                {

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    int index = tabViewer.TabPages.IndexOfKey("Welcome");

                    WelcomeTab appTab = new WelcomeTab();

                    tabViewer.TabPages[index].Controls.Add(appTab);
                }
                tabViewer.SelectTab("Welcome");
            }
            else if (e.Node.Name == "LogoutNode")
            {

                DialogResult result = MessageBox.Show("Are you sure you want to logout?", "Confirm Logout", MessageBoxButtons.OKCancel);
                if (result == System.Windows.Forms.DialogResult.OK)
                {
                    sessionManager = new SessionManager();
                    sessionManager.User = "";
                    sessionManager.Key = "";
                    tabViewer.TabPages.Clear();

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    int index = tabViewer.TabPages.IndexOfKey("Welcome");

                    WelcomeTab welcomeTab = new WelcomeTab();
                    tabViewer.TabPages[index].Controls.Add(welcomeTab);

                    updateNavTree();
                }
            }
        }
    }
}

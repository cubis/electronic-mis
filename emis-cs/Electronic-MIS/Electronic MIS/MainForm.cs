using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using iTextSharp;
using iTextSharp.text;
using iTextSharp.text.pdf;
using System.IO;
using System.Net;
using System.Security;
using System.Diagnostics;
using System.Xml;

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
            sessionManager.UserName = "";
            sessionManager.UserID = 0;
        }

        private void navigationTree_AfterSelect(object sender, TreeViewEventArgs e)
        {
            int index = 0;

            if (e.Node.Name == "LoginNode")
            {
                if(!tabViewer.TabPages.ContainsKey("Login")){

                    tabViewer.TabPages.Add("Login", "Login");
                    index = tabViewer.TabPages.IndexOfKey("Login"); 
                }
                tabViewer.SelectTab("Login");
                if (!tabViewer.TabPages[index].HasChildren)
                {
                    LoginTab login = new LoginTab();
                    login.LoginEvent += new LoginTab.LoginEventHandler(login_LoginEvent);
                    tabViewer.TabPages[index].Controls.Add(login);
                }
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
                    index = tabViewer.TabPages.IndexOfKey("PatientInfo");

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
                    index = tabViewer.TabPages.IndexOfKey("Appointments");

                    AppointmentTab appTab = new AppointmentTab(sessionManager,activeServer);
                    appTab.PrintEvent += new AppointmentTab.PrintEventHandler(appTab_PrintEvent);

                    tabViewer.TabPages[index].Controls.Add(appTab);
                }
                tabViewer.SelectTab("Appointments");
            }
            else if (e.Node.Name == "WelcomeNode")
            {
                if (!tabViewer.TabPages.ContainsKey("Welcome"))
                {

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    index = tabViewer.TabPages.IndexOfKey("Welcome");

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
                    sessionManager.UserName = "";
                    sessionManager.Key = "";
                    tabViewer.TabPages.Clear();

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    index = tabViewer.TabPages.IndexOfKey("Welcome");

                    WelcomeTab welcomeTab = new WelcomeTab();
                    tabViewer.TabPages[index].Controls.Add(welcomeTab);

                    updateNavTree();
                }
            }
        }

        void appTab_PrintEvent(object sender, PrintEventArgs e)
        {
           createPDF(e.Appointment, e.Copay);
        }

        /*                  Layout for the table
                        * 
                        * 
                        *       ELECTRONIC MEDICAL INFORMATION SYSTEMS
                        *      --------------------------------------------
                        *       Your Recipt for Wednesday, August 3, 2011
                        * 
                        * 
                        * Total Charges:     |$100.00
                        * Amount Paid  :     | $50.00
                        *                    ---------
                        * Remaining Balance: | $50.00
                        * 
                        * Your type of payment plan:  Monthly
                        * 
                        * */
        private void createPDF(Appointment appt, Decimal copay)
        {
            Cursor.Current = Cursors.WaitCursor;

            string path = "./Appt_" + appt.AppointmentID + ".pdf";
            if (File.Exists(path))
            {
                File.Delete(path);
            }

            //Create the Document
            FileStream fs = File.Create(path);
            Document document = new Document();
            PdfWriter writer = PdfWriter.GetInstance(document, fs);
            document.Open();

            //Create the Table to manage layout for the page
            PdfPTable table = new PdfPTable(1);
            table.HorizontalAlignment = Element.ALIGN_CENTER;

            //Add the logo
            Image logo = Image.GetInstance(Electronic_MIS.Properties.Resources.horizontal_logo, System.Drawing.Imaging.ImageFormat.Bmp);
            logo.Alignment = Image.ALIGN_CENTER;
            document.Add(logo);

            /*
            //Create the Title
            Font titleFont = new Font(iTextSharp.text.Font.FontFamily.COURIER, 18.0f);
            PdfPCell titleCell = new PdfPCell(new Phrase("ELECTRONIC MEDICAL INFORMATION SYSTEMS", titleFont));
            titleCell.HorizontalAlignment = Element.ALIGN_CENTER;
            titleCell.BorderWidth = 0;
            titleCell.BorderWidthBottom = 2f;
             */ 

            //Create the Subtitle
            PdfPCell subTitleCell = new PdfPCell(new Phrase("Your Recipt For " + appt.AppointmentTime.ToLongDateString() + " at " + appt.AppointmentTime.ToShortTimeString()));
            subTitleCell.HorizontalAlignment = Element.ALIGN_CENTER;
            subTitleCell.BorderWidth = 0;

            //Spacer
            PdfPCell spacer = new PdfPCell();
            spacer.FixedHeight = 100.0f;
            spacer.BorderWidth = 0;

            //Create a table for Appointment Summary
            PdfPTable appointmentSummaryTable = new PdfPTable(2);
            PdfPCell summaryLabelCell = new PdfPCell();
            summaryLabelCell.AddElement(new Phrase("Blood Pressure :"));
            summaryLabelCell.AddElement(new Phrase("Weight :"));
            summaryLabelCell.AddElement(new Phrase("Symptoms :"));
            summaryLabelCell.AddElement(new Phrase("Diagnosis :"));
            summaryLabelCell.HorizontalAlignment = Element.ALIGN_RIGHT;
            summaryLabelCell.Border = 0;
            PdfPCell summaryDataCell = new PdfPCell();
            summaryDataCell.AddElement(new Phrase(appt.BloodPressure));
            summaryDataCell.AddElement(new Phrase(appt.Weight));
            summaryDataCell.AddElement(new Phrase(appt.Symptoms));
            summaryDataCell.AddElement(new Phrase(appt.Diagnosis));
            summaryDataCell.HorizontalAlignment = Element.ALIGN_RIGHT;
            summaryDataCell.Border = 0;
            appointmentSummaryTable.AddCell(summaryLabelCell);
            appointmentSummaryTable.AddCell(summaryDataCell);

            decimal bill;
            if(appt.Bill != "")
            {
                bill = decimal.Parse(appt.Bill);
            }
            else
            {
                bill = 0;
            }
            //Create a table for summary charges
            PdfPTable chargesSummaryTable = new PdfPTable(2);
            PdfPCell summaryLabel = new PdfPCell(new Phrase("Summary of Charges :"));
            summaryLabel.HorizontalAlignment = Element.ALIGN_LEFT;
            summaryLabel.BorderWidth = 0;
            PdfPCell chargesCell = new PdfPCell();
            chargesCell.HorizontalAlignment = Element.ALIGN_RIGHT;
            chargesCell.AddElement(new Phrase("Basic Service Charge:  " + String.Format("{0:C}",(bill/2))));
            chargesCell.AddElement(new Phrase("Medical Supply Charge:  " + String.Format("{0:C}",(bill /4))));
            chargesCell.AddElement(new Phrase("Medicinal Charge:  " + String.Format("{0:C}",(bill /4))));
            chargesCell.HorizontalAlignment = Element.ALIGN_RIGHT;
            chargesCell.BorderWidth = 0;
            chargesSummaryTable.AddCell(summaryLabel);
            chargesSummaryTable.AddCell(chargesCell);
            
            
            //Create a subtable for the balance summary
            PdfPTable chargesTable = new PdfPTable(2);
            PdfPCell labelCell = new PdfPCell();
            labelCell.AddElement(new Phrase("Total Charges :\n"));
            labelCell.AddElement(new Phrase("Copay Amount  :\n"));
            labelCell.HorizontalAlignment = Element.ALIGN_RIGHT;
            labelCell.BorderWidth = 0;
            PdfPCell amountCell = new PdfPCell();
            amountCell.AddElement(new Phrase(String.Format("{0:C}",bill)));
            amountCell.AddElement(new Phrase(String.Format("{0:C}",copay)));
            amountCell.BorderWidth = 0;
            amountCell.HorizontalAlignment = Element.ALIGN_RIGHT;
            amountCell.BorderWidthBottom = 1f;
            PdfPCell totalLabel = new PdfPCell();
            totalLabel.AddElement(new Phrase("Remaining Balance :\n"));
            totalLabel.HorizontalAlignment = Element.ALIGN_RIGHT;
            totalLabel.BorderWidth = 0;
            PdfPCell balance = new PdfPCell();
            balance.AddElement(new Phrase(String.Format("{0:C}",copay)));
            balance.HorizontalAlignment = Element.ALIGN_RIGHT;
            balance.BorderWidth = 0;
            chargesTable.AddCell(labelCell);
            chargesTable.AddCell(amountCell);
            chargesTable.AddCell(totalLabel);
            chargesTable.AddCell(balance);


            //table.AddCell(titleCell);
            table.AddCell(subTitleCell);
            table.AddCell(appointmentSummaryTable);
            table.AddCell(spacer);
            table.AddCell(chargesSummaryTable);
            table.AddCell(spacer); 
            table.AddCell(chargesTable);


            document.Add(table);

            document.Close();

            System.Diagnostics.Process.Start(Path.GetDirectoryName(Application.ExecutablePath) + path);

            Cursor.Current = Cursors.Arrow;
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

            if (sessionManager.Key == "MEMBER PROFILE LOCKED")
            {
                MessageBox.Show("Your account has been locked.\nPlease contact an administrator.");
                return;
            }

            if(sessionManager.IsLoggedIn)
                tabViewer.TabPages.RemoveByKey("Login");
            this.Text = "Welcome to EMIS " + sessionManager.FirstName;
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
                this.Text = "Welcome to EMIS";
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
            int index = 0;

            if (e.Node.Name == "LoginNode")
            {
                if (!tabViewer.TabPages.ContainsKey("Login"))
                {

                    tabViewer.TabPages.Add("Login", "Login");
                    index = tabViewer.TabPages.IndexOfKey("Login");
                }
                tabViewer.SelectTab("Login");
                if (!tabViewer.TabPages[index].HasChildren)
                {
                    LoginTab login = new LoginTab();
                    login.LoginEvent += new LoginTab.LoginEventHandler(login_LoginEvent);
                    tabViewer.TabPages[index].Controls.Add(login);
                    tabViewer.TabPages[index].Controls[0].Focus();
                }
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
                    index = tabViewer.TabPages.IndexOfKey("PatientInfo");

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
                    index = tabViewer.TabPages.IndexOfKey("Appointments");

                    AppointmentTab appTab = new AppointmentTab(sessionManager, activeServer);
                    appTab.PrintEvent += new AppointmentTab.PrintEventHandler(appTab_PrintEvent);

                    tabViewer.TabPages[index].Controls.Add(appTab);
                }
                tabViewer.SelectTab("Appointments");
            }
            else if (e.Node.Name == "WelcomeNode")
            {
                if (!tabViewer.TabPages.ContainsKey("Welcome"))
                {

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    index = tabViewer.TabPages.IndexOfKey("Welcome");

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
                    sessionManager.UserName = "";
                    sessionManager.UserID = 0;
                    sessionManager.Key = "";
                    tabViewer.TabPages.Clear();

                    tabViewer.TabPages.Add("Welcome", "Welcome");
                    index = tabViewer.TabPages.IndexOfKey("Welcome");

                    WelcomeTab welcomeTab = new WelcomeTab();
                    tabViewer.TabPages[index].Controls.Add(welcomeTab);

                    updateNavTree();
                }
            }
        }

        private void tabViewer_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (tabViewer.SelectedIndex == -1)
                return;

            if (tabViewer.TabPages[tabViewer.SelectedIndex].HasChildren)
            {
                if (!tabViewer.TabPages[tabViewer.SelectedIndex].Controls[0].ContainsFocus)
                {
                    tabViewer.TabPages[tabViewer.SelectedIndex].Controls[0].Focus();
                }
            }
        }

    }
}

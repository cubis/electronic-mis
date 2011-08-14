namespace Electronic_MIS
{
    partial class AppointmentTab
    {
        /// <summary> 
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary> 
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Component Designer generated code

        /// <summary> 
        /// Required method for Designer support - do not modify 
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.calAppointments = new System.Windows.Forms.MonthCalendar();
            this.CancelButton = new System.Windows.Forms.Button();
            this.remindMeChkBox = new System.Windows.Forms.CheckBox();
            this.textBox1 = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.appointmentListBox = new System.Windows.Forms.ListBox();
            this.cmbAppointments = new System.Windows.Forms.ComboBox();
            this.btnReschedule = new System.Windows.Forms.Button();
            this.btnReciept = new System.Windows.Forms.Button();
            this.saveFileDialog1 = new System.Windows.Forms.SaveFileDialog();
            this.SuspendLayout();
            // 
            // calAppointments
            // 
            this.calAppointments.FirstDayOfWeek = System.Windows.Forms.Day.Sunday;
            this.calAppointments.Font = new System.Drawing.Font("Microsoft Sans Serif", 20.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.calAppointments.Location = new System.Drawing.Point(45, 49);
            this.calAppointments.MaxSelectionCount = 1;
            this.calAppointments.Name = "calAppointments";
            this.calAppointments.TabIndex = 1;
            this.calAppointments.DateChanged += new System.Windows.Forms.DateRangeEventHandler(this.calAppointments_DateChanged);
            // 
            // CancelButton
            // 
            this.CancelButton.Location = new System.Drawing.Point(449, 380);
            this.CancelButton.Name = "CancelButton";
            this.CancelButton.Size = new System.Drawing.Size(153, 23);
            this.CancelButton.TabIndex = 11;
            this.CancelButton.Text = "Cancel This Appointment";
            this.CancelButton.UseVisualStyleBackColor = true;
            this.CancelButton.Visible = false;
            this.CancelButton.Click += new System.EventHandler(this.CancelButton_Click);
            // 
            // remindMeChkBox
            // 
            this.remindMeChkBox.AutoSize = true;
            this.remindMeChkBox.CheckAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.remindMeChkBox.Location = new System.Drawing.Point(522, 357);
            this.remindMeChkBox.Name = "remindMeChkBox";
            this.remindMeChkBox.Size = new System.Drawing.Size(80, 17);
            this.remindMeChkBox.TabIndex = 10;
            this.remindMeChkBox.Text = "Remind Me";
            this.remindMeChkBox.UseVisualStyleBackColor = true;
            this.remindMeChkBox.Visible = false;
            this.remindMeChkBox.CheckedChanged += new System.EventHandler(this.remindMeChkBox_CheckedChanged);
            // 
            // textBox1
            // 
            this.textBox1.Location = new System.Drawing.Point(284, 82);
            this.textBox1.Multiline = true;
            this.textBox1.Name = "textBox1";
            this.textBox1.Size = new System.Drawing.Size(318, 263);
            this.textBox1.TabIndex = 9;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(42, 231);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(195, 16);
            this.label1.TabIndex = 8;
            this.label1.Text = "Your Current Appointments:";
            // 
            // appointmentListBox
            // 
            this.appointmentListBox.FormattingEnabled = true;
            this.appointmentListBox.Location = new System.Drawing.Point(45, 256);
            this.appointmentListBox.Name = "appointmentListBox";
            this.appointmentListBox.Size = new System.Drawing.Size(227, 147);
            this.appointmentListBox.TabIndex = 7;
            this.appointmentListBox.SelectedIndexChanged += new System.EventHandler(this.appointmentListBox_SelectedIndexChanged);
            // 
            // cmbAppointments
            // 
            this.cmbAppointments.FormattingEnabled = true;
            this.cmbAppointments.Location = new System.Drawing.Point(285, 49);
            this.cmbAppointments.Name = "cmbAppointments";
            this.cmbAppointments.Size = new System.Drawing.Size(264, 21);
            this.cmbAppointments.TabIndex = 12;
            this.cmbAppointments.SelectedIndexChanged += new System.EventHandler(this.comboBox1_SelectedIndexChanged);
            // 
            // btnReschedule
            // 
            this.btnReschedule.Location = new System.Drawing.Point(284, 380);
            this.btnReschedule.Name = "btnReschedule";
            this.btnReschedule.Size = new System.Drawing.Size(159, 23);
            this.btnReschedule.TabIndex = 13;
            this.btnReschedule.Text = "Reschedule this Appointment";
            this.btnReschedule.UseVisualStyleBackColor = true;
            this.btnReschedule.Visible = false;
            this.btnReschedule.Click += new System.EventHandler(this.btnReschedule_Click);
            // 
            // btnReciept
            // 
            this.btnReciept.Location = new System.Drawing.Point(366, 380);
            this.btnReciept.Name = "btnReciept";
            this.btnReciept.Size = new System.Drawing.Size(158, 23);
            this.btnReciept.TabIndex = 14;
            this.btnReciept.Text = "View Your Recipt";
            this.btnReciept.UseVisualStyleBackColor = true;
            this.btnReciept.Visible = false;
            this.btnReciept.Click += new System.EventHandler(this.btnReciept_Click);
            // 
            // AppointmentTab
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.Controls.Add(this.btnReciept);
            this.Controls.Add(this.btnReschedule);
            this.Controls.Add(this.cmbAppointments);
            this.Controls.Add(this.CancelButton);
            this.Controls.Add(this.remindMeChkBox);
            this.Controls.Add(this.textBox1);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.appointmentListBox);
            this.Controls.Add(this.calAppointments);
            this.Name = "AppointmentTab";
            this.Size = new System.Drawing.Size(615, 465);
            this.Load += new System.EventHandler(this.AppointmentTab_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.MonthCalendar calAppointments;
        private System.Windows.Forms.Button CancelButton;
        private System.Windows.Forms.CheckBox remindMeChkBox;
        private System.Windows.Forms.TextBox textBox1;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.ListBox appointmentListBox;
        private System.Windows.Forms.ComboBox cmbAppointments;
        private System.Windows.Forms.Button btnReschedule;
        private System.Windows.Forms.Button btnReciept;
        private System.Windows.Forms.SaveFileDialog saveFileDialog1;
    }
}

﻿namespace Electronic_MIS
{
    partial class Reschedule
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

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.dtDate = new System.Windows.Forms.DateTimePicker();
            this.cmbTimes = new System.Windows.Forms.ComboBox();
            this.cmbDocs = new System.Windows.Forms.ComboBox();
            this.btnOk = new System.Windows.Forms.Button();
            this.btnCancel = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // dtDate
            // 
            this.dtDate.Location = new System.Drawing.Point(40, 41);
            this.dtDate.Name = "dtDate";
            this.dtDate.Size = new System.Drawing.Size(200, 20);
            this.dtDate.TabIndex = 0;
            // 
            // cmbTimes
            // 
            this.cmbTimes.FormattingEnabled = true;
            this.cmbTimes.Location = new System.Drawing.Point(40, 85);
            this.cmbTimes.Name = "cmbTimes";
            this.cmbTimes.Size = new System.Drawing.Size(200, 21);
            this.cmbTimes.TabIndex = 1;
            this.cmbTimes.Text = "Choose a new appointment time.";
            // 
            // cmbDocs
            // 
            this.cmbDocs.FormattingEnabled = true;
            this.cmbDocs.Location = new System.Drawing.Point(40, 127);
            this.cmbDocs.Name = "cmbDocs";
            this.cmbDocs.Size = new System.Drawing.Size(200, 21);
            this.cmbDocs.Sorted = true;
            this.cmbDocs.TabIndex = 2;
            this.cmbDocs.Text = "Choose your prefered Doctor";
            // 
            // btnOk
            // 
            this.btnOk.Location = new System.Drawing.Point(40, 171);
            this.btnOk.Name = "btnOk";
            this.btnOk.Size = new System.Drawing.Size(75, 23);
            this.btnOk.TabIndex = 3;
            this.btnOk.Text = "Reschedule";
            this.btnOk.UseVisualStyleBackColor = true;
            this.btnOk.Click += new System.EventHandler(this.btnOk_Click);
            // 
            // btnCancel
            // 
            this.btnCancel.Location = new System.Drawing.Point(165, 171);
            this.btnCancel.Name = "btnCancel";
            this.btnCancel.Size = new System.Drawing.Size(75, 23);
            this.btnCancel.TabIndex = 4;
            this.btnCancel.Text = "Cancel";
            this.btnCancel.UseVisualStyleBackColor = true;
            this.btnCancel.Click += new System.EventHandler(this.btnCancel_Click);
            // 
            // Reschedule
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(281, 224);
            this.Controls.Add(this.btnCancel);
            this.Controls.Add(this.btnOk);
            this.Controls.Add(this.cmbDocs);
            this.Controls.Add(this.cmbTimes);
            this.Controls.Add(this.dtDate);
            this.Name = "Reschedule";
            this.Text = "Reschedule";
            this.Load += new System.EventHandler(this.Reschedule_Load);
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.DateTimePicker dtDate;
        private System.Windows.Forms.ComboBox cmbTimes;
        private System.Windows.Forms.ComboBox cmbDocs;
        private System.Windows.Forms.Button btnOk;
        private System.Windows.Forms.Button btnCancel;
    }
}
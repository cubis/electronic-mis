﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Electronic_MIS
{
    public enum Status
    {
        Scheduled,
        Canceled
    }

    public class Appointment : IComparable
    {
        String doc;
        String reason;
        String apptID;
        bool remind;
        DateTime appTime;
        int patID;
        int docID;
        String status;
        String patFirst;
        String patLast;


        public String Doctor
        {
            get
            {
                return doc;
            }
            set
            {
                doc = value;
            }

        }

        public String Reason
        {
            get
            {
                return reason;
            }
            set
            {
                reason = value;
            }

        }

        public String AppointmentID
        {
            get
            {
                return apptID;
            }
            set
            {
                apptID = value;
            }

        }

        public bool Remind
        {
            get
            {
                return remind;
            }
            set
            {
                remind = value;
            }

        }

        public DateTime AppointmentTime
        {
            get
            {
                return appTime;
            }
            set
            {
                appTime = value;
            }

        }

        public int PatientID
        {
            get
            {
                return patID;
            }
            set
            {
                patID = value;
            }
        }

        public int DoctorID
        {
            get
            {
                return docID;
            }
            set
            {
                docID = value;
            }
        }

        public String Status
        {
            get
            {
                return status;
            }
            set
            {
                status = value;
            }
        }

        public String PatientFirstName
        {
            get
            {
                return patFirst;
            }
            set
            {
                patFirst = value;
            }

        }

        public String PatientLastName
        {
            get
            {
                return patLast;
            }
            set
            {
                patLast = value;
            }

        }

        public override string ToString()
        {
            return (AppointmentTime.ToLongDateString() + ", " + AppointmentTime.ToLongTimeString());
        }

        public int CompareTo(object obj)
        {
            Appointment comp = (Appointment)obj;

            return DateTime.Compare(this.AppointmentTime, comp.AppointmentTime);
        }
    }
}
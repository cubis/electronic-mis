using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Electronic_MIS
{
    public class Doctor : IComparable
    {
        string docName;
        string docID;

        public String DoctorName
        {
            get
            {
                return docName;
            }
            set
            {
                docName = value;
            }
        }

        public String DoctorID
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

        public override string ToString()
        {
            return docName;
        }

        public int  CompareTo(object obj)
        {
            Doctor comp = (Doctor)obj;

            return docName.CompareTo(comp.ToString());
        }
    }
}

using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Electronic_MIS
{
    class Patient
    {
        String firstn;
        String lastn;
        String sex;
        String bday;
        String ssn;
        String email;
        String phonenum;
        String compname;
        String planType;
        String planNum;
        String covgPerc;
        String copay;
        String covgstart;
        String covgend;
        String doctornum;


        public String FirstName
        {
            get
            {
                return firstn;
            }
            set
            {
                firstn = value;
            }
        }

        public String LastName
        {
            get
            {
                return lastn;
            }
            set
            {
                lastn = value;
            }
        }

        public String Sex
        {
            get
            {
                return sex;
            }
            set
            {
                sex = value;
            }
        }

        public String Birthday
        {
            get
            {
                return bday;
            }
            set
            {
                bday = value;
            }
        }

        public String SSN
        {
            get
            {
                return ssn;
            }
            set
            {
                ssn = value;
            }
        }

        public String Email
        {
            get
            {
                return email;
            }
            set
            {
                email = value;
            }
        }

        public String phone
        {
            get
            {
                return phonenum;
            }
            set
            {
                phonenum = value;
            }
        }

        public String Company
        {
            get
            {
                return compname;
            }
            set
            {
                compname = value;
            }
        }

        public String PlanType
        {
            get
            {
                return planType;
            }
            set
            {
                planType = value;
            }
        }

        public String PlanNum
        {
            get
            {
                return planNum;
            }
            set
            {
                planNum = value;
            }
        }

        public String CoveragePercent
        {
            get
            {
                return covgPerc;
            }
            set
            {
                covgPerc = value;
            }
        }

        public String CoPay
        {
            get
            {
                return copay;
            }
            set
            {
                copay = value;
            }
        }

        public String CoverageStart
        {
            get
            {
                return covgstart;
            }
            set
            {
                covgstart = value;
            }
        }

        public String CoverageEnd
        {
            get
            {
                return covgend;
            }
            set
            {
                covgend = value;
            }
        }

        public string Doctor
        {
            get
            {
                return doctornum;
            }
            set
            {
                doctornum = value;
            }
        }

        public override string ToString()
        {
            return FirstName + " " + LastName + "\n";
        }
    }

}

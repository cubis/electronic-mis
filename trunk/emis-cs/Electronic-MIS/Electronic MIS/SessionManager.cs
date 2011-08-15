using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Electronic_MIS
{

    public class SessionManager
    {
        string userName;
        int userID;
        string key;
        int permissionLevel;
        bool loggedIn;
        string fname;
        string lname;

        /// <summary>
        /// Create a new SessionManager object
        /// </summary>
        public SessionManager()
        {

        }

        /// <summary>
        /// Create a new SessionManager object
        /// </summary>
        /// <param name="userName">Username</param>
        /// <param name="key">Hashed Key</param>
        public SessionManager(string userName, string key)
        {
            UserName = userName;
            Key = key;
        }

        /// <summary>
        /// Get or Set the UserName
        /// </summary>
        public string UserName
        {
            get
            {
                return userName;
            }
            set
            {
                userName = value;
            }
        }

        /// <summary>
        /// Get or Set the Key
        /// </summary>
        public string Key
        {
            get
            {
                return key;
            }
            set
            {
                key = value;
                if (key != "")
                {
                    loggedIn = true;
                }
                else
                {
                    loggedIn = false;
                }
            }
        }

        /// <summary>
        /// Get or Set the User's Permission Level
        /// </summary>
        public int UserPermissionLevel
        {
            get
            {
                return permissionLevel;
            }
            set
            {
                permissionLevel = value;
            }
        }

        /// <summary>
        /// Get the user's login status
        /// </summary>
        public bool IsLoggedIn
        {
            get
            {
                return loggedIn;
            }
        }

        /// <summary>
        /// The UserID for the current user
        /// </summary>
        public int UserID
        {
            get
            {
                return userID;
            }
            set
            {
                userID = value;
            }

        }

        public String FirstName
        {
            get
            {
                return fname;
            }
            set
            {
                fname = value;
            }

        }

        public String LastName
        {
            get
            {
                return lname;
            }
            set
            {
                lname = value;
            }
        }
    }
}

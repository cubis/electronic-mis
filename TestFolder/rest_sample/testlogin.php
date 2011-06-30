<?php
print("<HTML>\n");
print("<form name=\"loginform\" id=\"loginform\" method=\"post\" action=\"testdologin.php\">");
print("Login: <input type=\"text\" name=\"login\" id=\"login\" value=\"\" /><br />\n");
print("Password: <input type=\"password\" name=\"pw\" id=\"pw\" value=\"\" /><br />\n");
print("<input type=\"button\" name=\"button_login\" id=\"button_login\" value=\"Login\" onClick=\"submit();\">\n");
print("</form>\n");
print("</HTML>\n");
?>
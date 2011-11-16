<?php
/*
+-------------------------------------------------------------------------+
| Copyright (C) 2010-2011 The X-sys Group |
| |
| This program is free software; you can redistribute it and/or |
| modify it under the terms of the GNU General Public License |
| as published by the Free Software Foundation; either version 2 |
| of the License, or (at your option) any later version. |
| |
| This program is distributed in the hope that it will be useful, |
| but WITHOUT ANY WARRANTY; without even the implied warranty of |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the |
| GNU General Public License for more details. |
+-------------------------------------------------------------------------+
| Swmon: Solution For Switch Edge-Core ES3528M, ES3552M and ES3510 |
+-------------------------------------------------------------------------+
| This code is designed, written, and maintained by the X-sys Group. See |
| about.php and/or the AUTHORS file for specific developer information. |
+-------------------------------------------------------------------------+
| http://www.x-sys.com.ua/ |
+-------------------------------------------------------------------------+
*/

include("/usr/local/swmon/include/config.php");

$sql = "SELECT device.ip, ports.port 
        FROM device, ports
        WHERE device.id=ports.device_id 
        AND device.ip not like '%172.16.2%' 
        AND device.ip not like '%172.16.3%' 
        AND device.ip not like '%172.16.6%' 
        AND device.ip not like '%172.16.8%' 
        AND ports.name not like '%Unnamed%' 
        AND ports.name not like '%FAIL%' 
        AND ports.name not like '%_rezerv%' 
        AND ports.name not like '%_off%' 
        AND ports.trank=0";
$res = mysql_query($sql);

while($row = mysql_fetch_row($res)){
    /* Port off */
    snmpset("$row[0]","private","ifAdminStatus.$row[1]","i","1");
}

$URL = "/admin/groza";
header ("Refresh: 3; URL=".$URL);

print("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
       <html xmlns=\"http://www.w3.org/1999/xhtml\">
       <head>
         <title>Включение 1-й южный</title>
       <head>
       <body>
    	 <br />
         <center><h3>Включение 1-го южного<br />Готово</h3></center>
       </body>
       </html>");
?>

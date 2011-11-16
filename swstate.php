<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2010-2011 The X-sys Group                                 |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
 | Swmon: Solution For Switch Edge-Core ES3528M, ES3552M and ES3510        |
 +-------------------------------------------------------------------------+
 | This code is designed, written, and maintained by the X-sys Group. See  |
 | about.php and/or the AUTHORS file for specific developer information.   |
 +-------------------------------------------------------------------------+
 | http://www.x-sys.com.ua/                                                |
 +-------------------------------------------------------------------------+
*/

require('include/config.php');

?>

<html>
<head>
<title>Статус портов</title>
<meta http-equiv='refresh' content='300'>
<style type="text/css">
body {
  color: #444;
  font: normal 95% 'Droid Sans', arial, serif;
  margin:0; 
  padding:0;
}
a {
  text-decoration: none;
  color: #1e3e8c;
}
</style>
</head>
<body>
<table><tr>

<?php

/* Вывести полный список точек */
$sql = "SELECT id, name, ip
	FROM device
	WHERE id=$id
	ORDER BY name";
$selectdev = mysql_query($sql);
$rowsql = mysql_fetch_row($selectdev);

$sql = "SELECT p.port, p.name, p.mac, s.name, p.trank
	FROM device d
	LEFT JOIN ports p
	ON d.id=p.device_id
	LEFT JOIN state s
	ON d.state_id=s.id 
	WHERE d.id=$rowsql[0]
	ORDER BY port+0";
$show_sw = mysql_query($sql);

while($port_row = mysql_fetch_row($show_sw)) {
	/* Если сожержит одно из перечисленных то красить строку в указанный цвет */
	if($port_row[4] == 1 or $port_row[4] == 2) {
		$prst = snmpget("$rowsql[2]","private","ifOperStatus.$port_row[0]");
                if(ereg("Down",$prst)) {
                	$tr = "tr style=\"font-size: 70%; background: #ff6060\"";
			$td = "$port_row[1]";
                } else  {
			$tr = "tr style=\"font-size: 70%; background: #50ff50\"";
			$td = "$port_row[1]";
 		}
	} else if(ereg("FAIL",$port_row[1])) {
		$tr = "tr style=\"font-size: 70%; background: #d6d6ff\"";
                $td = "$port_row[1]";
	} else if(ereg("_rezerv",$port_row[1])) {
		$tr = "tr style=\"font-size: 70%; background: #d6d6ff\"";
		$td = "$port_row[1]";
	} else if(ereg("_off",$rowptr1[1])) {
                $tr = "tr style=\"font-size: 70%; background: #4a4a4a\"";
                $td = "$port_row[1]";
	} else if($port_row[3] == UP) {
		$prst = snmpget("$rowsql[2]","private","ifOperStatus.$port_row[0]");
                if(ereg("Down",$prst))
                {
	                $tr = "tr style=\"font-size: 70%; background: #c5f1c5\"";
                        $td = "$port_row[1]";
                } else {
                        $tr = "tr style=\"font-size: 70%; background: #86e086\"";
                        $td = "$port_row[1]";
                }
         } else {
		$tr = "tr style=\"font-size: 70%; background: #ccc\"";
                $td10 = "$port_row[1]";
         }


	if($port_row[3] == "DOWN") {
		$port_state = D;
	} else {
		$port_state = U;
	}

	/* Построение таблицы  */
	$table = "<$tr><td>$port_row[0]</td><td>$td</td><td><center>$port_row[2]</center></td><td><center>$port_state</center></td></tr>";
	
	/* Преобразование из цикла в строку */
	if(!end($table)) {
		$array_row_port .= $table."\n";
	}
}

/* Построение точки на карте */
echo "<td valign=\"top\"><table border=0 cellspacing=1 cellpadding=0 style=\"background: #fff\">\n";
echo "<tr bgcolor=\"#b0b0ff\" style=\"font-size: 80%\"><td colspan=4><center><b>$rowsql[1]</b></center></td></tr>\n";
echo "<tr><td width=18px></td><td width=160px></td><td width=20px></td><td width=20px></td></tr>\n";
echo "$array_row_port</table></td>\n";

?>

</tr></table>
</body>
</html>

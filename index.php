<?php

/*-------------------------------------------------------------------------+
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
 +-------------------------------------------------------------------------*/

require('include/config.php');

?>

<html>
<head>
<title>Monitoring SouthSide .NET</title>
  <link rel="stylesheet" href="chosen/style.css" />
  <link rel="stylesheet" href="chosen/chosen.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js" type="text/javascript"></script>
  <script src="chosen/chosen.proto.js" type="text/javascript"></script>
</head> 
<body>
<div id="container">
<?php
$sql = "SELECT name 
		FROM device
		WHERE snd=1 
		AND state_id=2";
$statedev = mysql_query($sql);
$numdev = mysql_num_rows($statedev);

if($numdev > 0) {
	echo "<a href=/><font color=\"red\"><h2>SouthSide .NET Monitoring</h2></font></a>\n";
} else {
	echo "<a href=/><font color=\"green\"><h2>SouthSide .NET Monitoring</h2></font></a>\n";
}
?>
| <a href=/yamaps.php>Карта</a> | <a href=/search.php>Поиск</a> | <a href=/admin/nodesc>Без подписи</a> | <a onclick="window.open('http://dhcp.ss.zp.ua','TIP','width=850,height=650,status=0,menubar=0,location=0,resizable=0,directories=0,toolbar=0,scrollbar=0');return false;" href="http://dhcp.ss.zp.ua/">DHCP</a>  | <a onclick="window.open('http://dhcp.ss.zp.ua/logdhcp.php','TIP','width=850,height=650,status=0,menubar=0,location=0,resizable=0,directories=0,toolbar=0,scrollbar=0');return false;" href="http://dhcp.ss.zp.ua/">DHCP Лог</a> |

<table>
<div class="side-by-side clearfix">
<form action = "?device" method = "post">
<td>
<div>
<select name="name" title="Выберите устройство..." class="chzn-select" style="width:150px;" id="name" tabindex="1">
    <option value="Dev">device</option>
</select>
</div>
</td>
<td>
<div>
<select name="num" title="Выберите номер..." class="chzn-select" style="width:60px;" id="number" tabindex="2">
	      <option value="%">-</option>
<?php for($j=1; $j < 68; $j++) {
	echo "<option value=\"$j\">$j</option>\n";
    }
?>
</select>
</div>
</td>
<td>
<div>
<select name="letter" title="Выберите букву..." class="chzn-select" style="width:50px;" id="letter" tabindex="3">
    <option value="/">-</option>
</select>
</div>
</td>
<td>
<input type = "submit" value = "OK"  tabindex="4" />
</td>
</form>
</div>
</table>

<?php
if(isset($_GET['device'])) {
	if ($name == NULL || $num == NULL){
    		echo "<font color=\"red\">Вы не выбрали устройство!</font>\n";
	} else {
		/* Вывести полный список точек */
		$sql = "SELECT d.id, d.name, d.ip, d.state_id, s.ports
				FROM device d
				LEFT JOIN snmp s ON d.snmp_id=s.id
				WHERE d.name like '%$name$num$letter%' 
				ORDER BY d.name";
		$selectdev = mysql_query($sql);
		$num_row = mysql_num_rows($selectdev);
		$count_table_prer_row = 6;
		$i = -1;

		/* Проверка наличие свича */
		if ($num_row == 0) {
			echo "<font color=\"red\">Подумай, может выбрал не тот свитч?</font>\n";
		} else {
			echo "<table><tr>\n";

			/* Вывести полный список свичей */
			while($rowsql = mysql_fetch_row($selectdev)){
				/* Если вклчючен то показывать точку зеленым */
				if($rowsql[3] == 1) {
					$sql = "SELECT p.port, p.name, p.mac, s.name, p.trank
							FROM ports p
							LEFT JOIN device d ON p.device_id=d.id
							LEFT JOIN state s ON p.state_id=s.id
							WHERE d.id=$rowsql[0] 
							ORDER BY port+0";
					$show_sw_up = mysql_query($sql);

					while($port_row = mysql_fetch_row($show_sw_up)) {
						/* Если сожержит одно из перечисленных то красить строку в указанный цвет */
						if($port_row[4] == 1 or $port_row[4] == 2) {
							$prst = snmpget("$rowsql[2]","private","ifOperStatus.$port_row[0]");
							if(ereg("Down",$prst)) {	
								$tr = "tr style=\"font-size: 70%; background: #ff6060\"";
								$td = "<a href=trank.php?id=$rowsql[0]&port=$rport_row[0]>$port_row[1]</a>";
							} else {
								$tr = "tr style=\"font-size: 70%; background: #50ff50\"";
								$td = "<a href=trank.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
							}
						} else if(ereg("FAIL",$port_row[1])) {
							$tr = "tr style=\"font-size: 70%; background: #d6d6ff\"";
							$td = "$port_row[1]";
						} else if(ereg("_rezerv",$port_row[1])) {
							$tr = "tr style=\"font-size: 70%; background: #d6d6ff\"";
							$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
						} else if(ereg("_off",$port_row[1])) {
							$tr = "tr style=\"font-size: 70%; background: #5e5e5e\"";
							$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
						} else if($port_row[3] == "UP") {
							$tr = "tr style=\"font-size: 70%; background: #c5f1c5\"";
							$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
						} else {
							$tr = "tr style=\"font-size: 70%; background: #cccccc\"";
							$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
						}

						if($port_row[3] == "DOWN") {
						    $port_state = "D";
						} else {
						    $port_state = "U";
						}

						/* Построение таблицы  */
						$table = "<$tr><td>$port_row[0]</td><td>$td</td><td><a href=mac.php?id=$rowsql[0]&port=$port_row[0]><center>$port_row[2]</center></a></td><td><center>$port_state</center></td></tr>";
	
						/* Преобразование из цикла в строку */
						if(!end($table)) {
							$array_row_port .= $table."\n";
						}
					}
	
				$i++;
        	 		if ($i == $count_table_prer_row)
				{	
					echo '<tr>';
					$i = 0;   
				}

				/* Построение таблицы */
		        	echo "<td valign=\"top\">\n<table border=0 cellspacing=1 cellpadding=0 style=\"background: #fff\">\n";
		        	echo "<tr bgcolor=\"#b0b0ff\" style=\"font-size: 80%\"><td colspan=1><center><a onclick=\"window.open('http://geo.ss.zp.ua/swstate.php?id=$rowsql[0]','TIP','width=230,height=460,status=0,menubar=0,location=0,resizable=0,directories=0,toolbar=0,scrollbar=0');return false;\" href=http://geo.ss.zp.ua/swstate.php?id=$rowsql[0]><b>S</b></a>_<a href=telnet://$rowsql[2]><b>T</b></a></center></td><td colspan=3><a href=/admin/sw/cngsw.php?id=$rowsql[0]><center><b>$rowsql[1]</b></a></center></td></tr>\n";
		        	echo "<tr><td width=23px></td><td width=160px></td><td width=20px></td><td width=20px></td></tr>\n";
		        	echo "$array_row_port</table></td>\n";

				/* Очишаем переменную для следующего круга */
				$array_row_port = "";

			/* Если выклчючен то показывать точку красным */
    			} else {
				$sql = "SELECT p.port, p.name, p.mac, s.name
						FROM ports p
						LEFT JOIN device d ON p.device_id=d.id
						LEFT JOIN state s ON p.state_id=s.id
						WHERE d.id=$rowsql[0]
						ORDER BY port+0";
				$show_sw_down = mysql_query($sql);

				while($port_row = mysql_fetch_row($show_sw_down)) {
					if($port_row[3] == "DOWN"){
				    		$port_stste = "D";
					} else {
					    	$port_stste = "U";
					}

					/* Построение таблицы  */
					$table = "<tr style=\"font-size: 70%; background: #ff6060\"><td>$port_row[0]</td><td>$port_row[1]</td><td><center>$port_row[2]</center></td><td><center>$port_stste</center></td></tr>";

					/* Преобразование из цикла в строку */
					if(!end($table)){
						$array_row_port .= $table."\n";
					}
				}

	                	$i++;   
        	        	if ($i == $count_table_prer_row)
        	        	{
        	                	echo '<tr>';
        	                	$i = 0;
        	        	}

				/* Построение таблицы */
		        	echo "<td valign=\"top\"><table border=0 cellspacing=1 cellpadding=0 style=\"background: #fff\">\n";
     				echo "<tr bgcolor=\"#b0b0ff\" style=\"font-size: 80%\"><td colspan=1><center><a href=telnet://$rowsql[2]><b>T</b></a></center></td><td colspan=3><a href=/admin/sw/cngsw.php?id=$rowsql[0]><center><b>$rowsql[1]</b></a></center></td></tr>\n";
		        	echo "<tr><td width=25px></td><td width=160px></td><td width=20px></td><td width=20px></td></tr>\n";
		        	echo "$array_row_port</table></td>\n";

				/* Очишаем переменную для следующего круга */
				$array_row_port = "";
	    		}
			}
		}
	}
}
?>
</tr></table>
</div>
</body>
</html>

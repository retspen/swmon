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

include(dirname(__FILE__) . "/include/config.php");

/* Выводит список свитчей */
$sql = "SELECT d.id, d.name, d.ip, s.ports, s.snmp1 
		FROM device d 
		LEFT JOIN snmp s 
		ON d.snmp_id=s.id 
		WHERE d.state_id=1";
$alldev = mysql_query($sql);

/* Цикл по кол-ву свитчей */
while($rowsql = mysql_fetch_row($alldev)){

for($i=1;$i<=$rowsql[3];$i++) {
	/* Обнулить значение MAC на порту */
	$sql = "UPDATE ports SET mac=0 WHERE device_id=$rowsql[0] AND port=$i";
	$setnull = mysql_query($sql);
	
	/* Получить имя порта */
	$getname = snmpget("$rowsql[2]", "public", "$rowsql[4].$i");
	$strname = substr($getname, 9, strlen($getname)-10);

	/* Если порт изменен то внести это в логи */
	$sql = "SELECT name FROM ports WHERE device_id=$rowsql[0] AND port=$i";
	$check_name = mysql_query($sql);
	$check_name_row = mysql_fetch_row($check_name);

	/* Если свитч стал не доступен то не чего не менять*/
	if($getname != NULL) {
		if($check_name_row[0] != $strname && $check_name_row[0] != Unnamed && $strname == NULL) {
			$today = date("H:i d.m.y");
			$sql = "INSERT INTO `logports` (device_id, port, date, name) VALUES ('$rowsql[0]','$i','$today','Unnamed')";
			$change_name = mysql_query($sql);
		} else if($strname != NULL && $check_name_row[0] != $strname) {
			$today = date("H:i d.m.y");
			$sql = "INSERT INTO `logports` (device_id, port, date, name) VALUES ('$rowsql[0]','$i','$today','$strname')";
			$change_name = mysql_query($sql);
		}
		/* Обновить имя порта в базе если не NULL или Unnamed если NULL*/
	        $sql = "UPDATE ports SET name='".($strname != NULL?$strname:'Unnamed')."' WHERE device_id=$rowsql[0] AND port=$i";
		$setname = mysql_query($sql);
	}

	/* Получить состояние порта */
	$getstate = snmpget("$rowsql[2]", "public", "ifAdminStatus.$i");
        $strstate = substr($getstate, 9, strlen($getstate)-12);

	/* Если свитч стал не доступен то не чего не менять*/
	if($getstate != NULL) {
		/* Обновление значение в базе для включен */
		if($strstate == "up") {
			$sql = "UPDATE ports SET state_id=1 WHERE device_id=$rowsql[0] AND port=$i";
		    	$setstate = mysql_query($sql);
		/* Обновление значение в базе для выключен */
	        } else {
			$sql = "UPDATE ports SET state_id=2 WHERE device_id=$rowsql[0] AND port=$i";
			$setstate = mysql_query($sql);
		}
	}

	if($getstate != NULL && ereg("OFFICE",$strname) or ereg("40L.",$strname) or ereg("AVT.",$strname) or ereg("GAV.",$strname) or ereg("NG.",$strname) or ereg("NOV.",$strname) or ereg("ST.",$strname) or ereg("TRUNK",$strname) or ereg("PODVAL",$strname)) {
		/* Если состояние транкового порта изменено то внести это в логи */
		$sql = "SELECT trank FROM ports WHERE device_id=$rowsql[0] AND port=$i";
		$check_trank = mysql_query($sql);
		$check_trank_row = mysql_fetch_row($check_trank);

		$prst = snmpget("$rowsql[2]","public","ifOperStatus.$i");
		$strprst = substr($prst, 9, strlen($prst)-12);

		if($check_trank_row[0] == 2 && $strprst == "up") {
			$today = date("H:i d.m.y");
			$sql = "INSERT INTO `statetrank` (device_id, port, date, state_id) VALUES ('$rowsql[0]','$i','$today','1')";
			$change_trank = mysql_query($sql);
		} else if($state_trank[0] == 1 && $strprst == "lowerLayerDown") {
			$today = date("H:i d.m.y");
			$sql = "INSERT INTO `statetrank` (device_id, port, date, state_id) VALUES ('$rowsql[0]','$i','$today','2')";
			$change_trank = mysql_query($sql);
		}

		/* Само состояние порта */
		if($strprst == "up") {
			$sql = "UPDATE ports SET trank=1 WHERE device_id=$rowsql[0] AND port=$i";
			$state_trank = mysql_query($sql);
		} else {
			$sql = "UPDATE ports SET trank=2 WHERE device_id=$rowsql[0] AND port=$i";
			$state_trank = mysql_query($sql);
		}
	} else {
		$sql = "UPDATE ports SET trank=0 WHERE device_id=$rowsql[0] AND port=$i";
		$state_ports = mysql_query($sql);
	}
}
	/* Получить кол-во mac на всех портах свитча */
    	$getmac = snmpwalk("$rowsql[2]", "public", ".1.3.6.1.2.1.17.4.3.1.2.0");

    	/* Почучить данные с масива и кол-во повторений */
    	$arr = array_count_values($getmac);
    	if(is_array($arr)) {
		reset($arr);

        	while(list($k,$v) = each($arr)) {
    	    	$a = substr($k, 9, strlen($k)-9);

    	    	/* Обновить кол-во MAC на порту */
            	$sql = "UPDATE ports SET mac='$v' WHERE device_id=$rowsql[0] AND port=$a";
	    	$mac = mysql_query($sql);
        }
    }
}

?>

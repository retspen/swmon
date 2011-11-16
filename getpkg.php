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

/* Удалить все картинки с загрузками на портах */
system("rm $homedir/tmp/*");

/* Выводит список портов */
$sql = "SELECT d.id, d.ip, p.port
	FROM device d 
	LEFT JOIN ports p 
	ON d.id=p.device_id 
	WHERE d.state_id=1
	AND p.state_id=1
	AND p.trank=0
	AND p.name not like '%FAIL%' 
	AND p.name not like '%rezerv%'";
$alldev = mysql_query($sql);

while($rowoct = mysql_fetch_row($alldev)){

    if(@fopen("$homedir/rrd/".$rowoct[0]."_".$rowoct[2].".rrd", "r")) {
	/* Получить данные с порта */
	$getinoct = snmpget("$rowoct[1]", "public", "ifInOctets.$rowoct[2]");
	$strinoct = substr($getinoct, 11, strlen($getinoct)-11);
	
	$getoutoct = snmpget("$rowoct[1]", "public", "ifOutOctets.$rowoct[2]");
	$stroutoct = substr($getoutoct, 11, strlen($getoutoct)-11);

	/* Относительно пользователя, для относительно оборудования поменять местами $str*oct */
	rrd_update("$homedir/rrd/".$rowoct[0]."_".$rowoct[2].".rrd","N:$stroutoct:$strinoct");
    } else {
	$rrdadd = array( "--step", "300", "--start", 0,
			"DS:in:COUNTER:600:U:U",
			"DS:out:COUNTER:600:U:U",
			"RRA:AVERAGE:0.5:1:600",
			"RRA:AVERAGE:0.5:6:700",
			"RRA:AVERAGE:0.5:24:775",
			"RRA:AVERAGE:0.5:288:797",
			"RRA:MAX:0.5:1:600",
			"RRA:MAX:0.5:6:700",
			"RRA:MAX:0.5:24:775",
			"RRA:MAX:0.5:288:797"
			);
	rrd_create("$homedir/rrd/".$rowoct[0]."_".$rowoct[2].".rrd", $rrdadd, count($rrdadd));
    }
}

?>

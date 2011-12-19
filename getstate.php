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
$sql = "SELECT id, ip FROM device";
$alldev = mysql_query($sql);

/* Цикл по кол-ву свитчей */
while($rowsql = mysql_fetch_row($alldev)){

    $state = snmpget("$rowsql[1]", "public", "sysName.0");
    exec("ping -c 1 -w 10 $rowsql[1]",$output, $stateping);

    /* Обновление статуса свитча */
    if($state == NULL and $stateping == 0) {
	$sql = "UPDATE device SET state_id=2 WHERE id=$rowsql[0]";
	$swstate = mysql_query($sql);

    } else {
	$sql = "UPDATE device SET state_id=1 WHERE id=$rowsql[0]";
        $swstate = mysql_query($sql);
    }

}

$sql = "SELECT id, name FROM device WHERE state_id=2 AND snd=0";
$resnd = mysql_query($sql);
while($rowsnd = mysql_fetch_row($resnd)){

	$today = date("H:i d.m.y");

	/* Запись логов устройства */
	$sql = "INSERT INTO `logdevice` (device_id, state_id, date) VALUE ('$rowsnd[0]','2', '$today')";
	$swlogdown = mysql_query($sql);

	/* Установка маркера об отсылке СМС */
    $sql = "UPDATE device SET snd=1 WHERE state_id=2 AND id=$rowsnd[0]";
	$swdown = mysql_query($sql);

	/* Преобразование из массива в строку */
	if(!end($rowsnd[1])){
            $strdown.= $rowsnd[1].'; ';
        }
}

$sql = "SELECT id, name FROM device WHERE state_id=1 AND snd=1";
$resupd = mysql_query($sql);
while($rowupd = mysql_fetch_row($resupd)){

	$today = date("H:i d.m.y");

	/* Запись логов устройства */
	$sql = "INSERT INTO `logdevice` (device_id, state_id, date) VALUE ('$rowupd[0]','1', '$today')";
	$swlogup = mysql_query($sql);

	/* Обновление статуса отправки и состояния */
    $sql = "UPDATE device SET snd=0 WHERE state_id=1 AND id=$rowupd[0]";
	$swup = mysql_query($sql);

	/* Преобразование из массива в строку */
	if(!end($rowupd[1])){
            $strup.= $rowupd[1].'; ';
        }
}

/* Если свитч не доступен отправить сообщение c фиксацией времени*/
$sql = "SELECT email FROM notif WHERE enable=1";
$resnfdn = mysql_query($sql);
while($rownfdn = mysql_fetch_row($resnfdn)){
    if($strdown != NULL) {
        $today = date("H:i d.m.y");
        $message = "$strdown ($today)";
        $headers = 'From: GEO <noc@ss.zp.ua>' . "\r\n" .
                   'Reply-To: noc@ss.zp.ua' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
        mail($rownfdn[0], 'Switch DOWN', $message, $headers);
	sleep(25);
    }
}

/* Если свитч доступен отправить сообщение с фиксацией времени*/
$sql = "SELECT email FROM notif WHERE enable=1";
$resnfup = mysql_query($sql);
while($rownfup = mysql_fetch_row($resnfup)){
    if($strup != NULL) {
        $today = date("H:i d.m.y");
        $message = "$strup ($today)";
        $headers = 'From: GEO <noc@ss.zp.ua>' . "\r\n" .
                   'Reply-To: noc@ss.zp.ua' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
        mail($rownfup[0], 'Switch UP', $message, $headers);
	sleep(25);
    }
}

?>

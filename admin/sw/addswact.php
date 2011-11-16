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

include("../../include/config.php");
    
    /* Добавляем устройство */
    $sql = "INSERT INTO `device` (name, ip, snmp_id) VALUES ('$name','$ip','$snmp')";
    $res = mysql_query($sql);

    /* Возвращаем его ID для создания привязки портов */
    $last_id = mysql_insert_id();
    
    /* Если кол-во введенных портов не больше 10 */
    if($snmp == 1 or $snmp == 4)
    {
	$sql = "INSERT INTO `ports` (device_id, port) VALUES ('$last_id','1'),('$last_id','2'),('$last_id','3'),('$last_id','4'),('$last_id','5'),('$last_id','6'),('$last_id','7'),('$last_id','8'),('$last_id','9'),('$last_id','10')"; 
	$res10 = mysql_query($sql);
    }
    /* Если кол-во введенных портов не больше 28 */
    else if ($snmp == 2)
    {
	$sql = "INSERT INTO `ports` (device_id, port) VALUES ('$last_id','1'),('$last_id','2'),('$last_id','3'),('$last_id','4'),('$last_id','5'),('$last_id','6'),('$last_id','7'),('$last_id','8'),('$last_id','9'),('$last_id','10'),('$last_id','11'),('$last_id','12'),('$last_id','13'),('$last_id','14'),('$last_id','15'),('$last_id','16'),('$last_id','17'),('$last_id','18'),('$last_id','19'),('$last_id','20'),('$last_id','21'),('$last_id','22'),('$last_id','23'),('$last_id','24'),('$last_id','25'),('$last_id','26'),('$last_id','27'),('$last_id','28')";
	$res28 = mysql_query($sql);
    }
    else if ($snmp == 3)
    {
	$sql = "INSERT INTO `ports` (device_id, port) VALUES ('$last_id','1'),('$last_id','2'),('$last_id','3'),('$last_id','4'),('$last_id','5'),('$last_id','6'),('$last_id','7'),('$last_id','8'),('$last_id','9'),('$last_id','10'),('$last_id','11'),('$last_id','12'), ('$last_id','13'),('$last_id','14'),('$last_id','15'),('$last_id','16'),('$last_id','17'),('$last_id','18'),('$last_id','19'),('$last_id','20'),('$last_id','21'),('$last_id','22'),('$last_id','23'),('$last_id','24'),('$last_id','25'),('$last_id','26'),('$last_id','27'),('$last_id','28'),('$last_id','29'),('$last_id','30'),('$last_id','31'),('$last_id','32'),('$last_id','33'),('$last_id','34'),('$last_id','35'),('$last_id','36'),('$last_id','37'),('$last_id','38'),('$last_id','39'),('$last_id','40'),('$last_id','41'),('$last_id','42'),('$last_id','43'),('$last_id','44'),('$last_id','45'),('$last_id','46'),('$last_id','47'),('$last_id','48'),('$last_id','49'),('$last_id','50'),('$last_id','51'),('$last_id','52')";
	$res48 = mysql_query($sql);
    }

    /* Добавляем устройство */
    $sql = "INSERT INTO `geo` (device_id, point, link) VALUES ('$last_id','$point','$link')";
    $resgeo = mysql_query($sql);

	$URL = "/";
        header ("Refresh: 3; URL=".$URL);
        print("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
                        <html xmlns=\"http://www.w3.org/1999/xhtml\">
                        <head>
                        <title>Устройство</title>
                        <head>
                        <body>
                        <br />
                        <center><h3>Успешно добавлено</h3></center>
                        </body>
                        </html>");
?>


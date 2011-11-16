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
<title>Monitoring SouthSide .NET</title>
<style type="text/css">
body {
  color: #444;
  font: normal 95% 'Droid Sans', arial, serif;
  margin:0;
  padding:0;
}
a {
  text-decoration: none;
  color: #112046;
}
</style>
</head>
<body>
<center><h2>Лог порта №<?php echo $port; ?></h2>

<?php if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; }

$sql = "SELECT port, name, date, id 
	FROM logports 
	WHERE device_id=$id 
	AND port=$port 
	ORDER BY id+0 DESC";
$res = mysql_query($sql);

$i = 1;
$num = mysql_num_rows($res);

if($num > 0)
{
    echo "<br /><br /><table border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr bgcolor=\"#72ff72\" align=center><td>№</td><td>Порт:</td><td>Название:</td><td>Дата Изменения:</td></tr>";

    while($row = mysql_fetch_row($res)){

	echo "<tr bgcolor=\"#65bcff\" align=center><td>$i</td><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td></tr>";
	$i++;
    }
    echo "</table><br />";
}
else
{
    echo "<br /><br /><font color=grey>Логи отсутствуют</font><br /><br />";
}

if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; } ?>
</center>
</body>
</html>

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
  <link rel="stylesheet" href="chosen/style.css" />
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
| <a href=/yamaps.php>Карта</a> | 
<br />
<br />
<form action="" method="get">
  <input type="text" placeholder="Введите название" name="username" value="">
</form>

<?php
/* Поиск пользователя */
if(isset($_GET['username'])) {
	if ($username == NULL){
		echo "<font color=\"red\">Вы ввели логин пользователя!</font>\n";
	} else {
		/* Вывести список точек */
		$sql = "SELECT d.id, d.ip, d.name, p.name, p.port
			FROM device d
			LEFT JOIN ports p ON d.id=p.device_id
			WHERE p.name like '%$username%'
			AND p.trank=0";
		$selectuser = mysql_query($sql);

		$i = 0;

		while($user_row = mysql_fetch_row($selectuser)) {
    			/* Отрезаем кусок до токи */
			$regexp = preg_replace("#[^\.]*\.#s",'',$user_row[3]);
			/* Записываем запрос для в массив для сортировки*/
			$array[$i] = array($regexp,$user_row[0],$user_row[1],$user_row[2],$user_row[4]);
			$i++;
		}

		if ($array == NULL) {
			echo "<font color=\"red\">Такого пользователя не найдено :(</font>\n";
		} else {
			sort($array);
			/* Заголовок таблицы таблицы */
			echo "<table border=0 cellspacing=1 cellpadding=0 style=\"background: #fff\">\n";
			echo "<tr style=\"font-size: 95%; background: #72ff72\"><td>№</td><td align=\"center\" width=120px>Устройсво:</td><td>Порт:</td><td align=\"center\" width=160px>Название:</td></tr>\n";

			$cn = 1;

			for($j=0; $j < count($array); $j++) {
				/* Отрезаем все после точки */
				$regdevname = substr($array[$j][3],0,strpos($array[$j][3],'.')+1);
				/* Отрезаем все до точки включельно */
				$regdevnum = preg_replace("#[^\.]*\.#s",'',$array[$j][3]);
				/* Заполняем таблицу */
				echo "<tr style=\"font-size: 95%; background: #65bcff\"><td>$cn</td><td align=\"center\"><a href=/?name=$regdevname&num=$regdevnum&device>".$array[$j][3]."</a></td><td align=\"center\">".$array[$j][4]."</td><td align=\"center\"><a href=port.php?id=".$array[$j][1]."&port=".$array[$j][4].">".$array[$j][0]."</a></td></tr>";
   				$cn++;
    			}
			echo "</table>";
		}
	}
}
?>
</div>
</body>
</html>

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
<center><h2>Логи портов</h2>

| <a href=/>Главная</a> <?php if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; }

include("../../include/config.php");

$sql = "SELECT device.id, device.ip, device.name, logports.port, logports.name, logports.date, logports.id 
	FROM logports, device 
	WHERE device.id=device_id 
	ORDER BY logports.id+0 DESC 
	LIMIT 0,30";
$res = mysql_query($sql);

$i = 1;

echo "<br><br><table border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr bgcolor=\"#72ff72\" align=center><td>№</td><td>Устройство:</td><td>Порт:</td><td>Название:</td><td>Дата изменения:</td></tr>";

while($row = @mysql_fetch_row($res)){

    echo "<tr bgcolor=\"#65bcff\" align=center><td>$i</td><td><a href=telnet://$row[1]>$row[2]</a></td><td>$row[3]</td><td><a href=/port.php?id=$row[0]&port=$row[3]>$row[4]</td><td>$row[5]</td></tr>";

    $i++;

    }

echo "</table><br>";
?>
| <a href=/>Главная</a> <?php if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; } ?>
</center>
</body>
</html>

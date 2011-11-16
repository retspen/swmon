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
<center><h2>Устройства</h2>

| <a href=/>Главная</a> <?php if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; }

include("../../include/config.php");

$sql = "SELECT device.ip, device.name, logdevice.date, state.name  
	FROM device, state, logdevice 
	WHERE device.id=logdevice.device_id 
	AND state.id=logdevice.state_id 
	ORDER BY logdevice.id+0 DESC 
	LIMIT 0,30";
$res = mysql_query($sql);

$i = 1;

echo "<br /><br /><table border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr bgcolor=\"#72ff72\" align=center><td>№</td><td>Устройство:</td><td>Дата:</td><td>Состояние:</td></tr>";

while($row = @mysql_fetch_row($res)){

    echo "<tr bgcolor=\"#65bcff\" align=center><td>$i</td><td><a href=telnet://$row[0]>$row[1]</a></td><td>$row[2]</td><td>$row[3]</td></tr>";
    $i++;
}
echo "</table><br />";
?>

| <a href=/>Главная</a> <?php if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; } ?>

</center>
</body>
</html>

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
<center><h2>Monitoring SouthSide .NET</h2>

| <a href=/>Назад</a> |

<?php 

include("../../include/config.php");

$sql = "SELECT device.name, device.ip, ports.port, device.id 
	FROM device, ports 
	WHERE device.id=ports.device_id 
	AND device.state_id=1 
	AND ports.mac=1 
	AND ports.name='Unnamed'";
$res = mysql_query($sql);

$i = 1;
$num = mysql_num_rows($res);

if($num > 0)
{
    echo "<br /><br /><table border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr bgcolor=\"#72ff72\" align=center><td>№</td><td>Устройство:</td><td>Port:</td></tr>";

    while($row = @mysql_fetch_row($res)){

	echo "<tr bgcolor=\"#65bcff\" align=center><td>$i</td><td><a href=telnet://$row[1]>$row[0]</a></td><td><a href=/port.php?id=$row[3]&port=$row[2]>$row[2]</a></td></tr>";
	$i++;
    }
    echo "</table><br />";
}
else
{
    echo "<br /><br /><font color=grey>Порты без подписи отсутсвуют</font><br /><br />";
}
?>

| <a href=/>Назад</a> |

</center>
</body>
</html>

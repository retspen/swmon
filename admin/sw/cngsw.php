<html>
<head>
<title>Monitoring SouthSide .NET </title>
<style type="text/css">
body {
  color: #444;
  font: normal 95% 'Droid Sans', arial, serif;
  margin: 0;
  padding: 0;
  text-align: center;
}
a {
  text-decoration: none;
  color: #112046;
}
table {
  margin-left: auto;
  margin-right: auto;
}
</style>
</head>
<body>
<a href=/><h2>Monitorig SouthSide .NET</h2></a>
<h4>Изменить устройство</h4>

<?php

include("../../include/config.php");

$sql = "SELECT device.id, device.name, device.ip, geo.point, geo.link FROM device, geo WHERE device.id='$id' AND geo.device_id = device.id";
$res = mysql_query($sql,$dbconnect);
$row = mysql_fetch_row($res);

if(isset($_GET['reload'])) {
    system("$homedir/bin/reload.pl $row[2]");
    header("location:/");
    die();
}

?>

<form method = "post"
      action = "cngswact.php?id=<?php echo "$row[0]"; ?>">
<table>
<tr>
<td>Имя свитча:</td>
<td>
<input type = text
       name = "name"
       value = "<?php echo "$row[1]"; ?>"
       size = "16">
</td>
</tr>
<tr>
<td>IP адрес:</td>
<td>
<input type = "text" 
       name = "ip" 
       value = "<?php echo "$row[2]"; ?>"
       size = "16">
</td>
</tr>
<tr>
<td>GPS точка:</td>
<td>
<input type = "text" 
       name = "point"
       value = "<?php echo "$row[3]"; ?>"
       size = "16">
</td>
</tr>
<tr>
<td>GPS линк:</td>
<td>
<input type = "text" 
       name = "link"
       value = "<?php echo "$row[4]"; ?>"
       size = "16">
</td>
</tr>
</table><br>
<input type = "submit" value = "Изменить" onclick="return confirm('Вы уверены?')">

<script>
function conf() {
    if (confirm('Вы уверены?'))
        location.href = "delswact.php?name=<?php echo $row[1]; ?>";
    }
</script>
<input type="button" value="Удалить" onclick="javascript:conf()">

<script>
function reload() {
    if (confirm('Вы уверены?'))
        location.href = "cngsw.php?id=<?php echo $row[0]; ?>&reload";
    }
</script>
<input type="button" value="Ребут" onclick="javascript:reload()">
</form>

| <a href=http://api.yandex.ru/maps/tools/getlonglat/>Определение координат</a> |

</body>
</html>
        

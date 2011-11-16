<html>
<head>
<title>Monitoring SouthSide .NET </title>
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
<center>
<h2>Monitorig SouthSide .NET</h2>
<h4>Изменить данные</h4>

<?php

include("../../include/config.php");

$sql = "SELECT id, name, email, enable 
	FROM notif 
	WHERE id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_row($res);

?>

<form method = "post"
      action = "cngphoneact.php?id=<?php echo "$row[0]"; ?>">
<table>
<tr>
<td>Название:</td>
<td>
<input type = text
       name = "name"
       value = "<?php echo "$row[1]"; ?>"
       size = "16">
</td>
</tr>
<tr>
<td>Email:</td>
<td>
<input type = "text"
       name = "email"
       value = "<?php echo "$row[2]"; ?>"
       size = "16">
</td>
</tr>
<tr>
<td>Активен:</td>
<td>
<select name = enable>
    <option value = "1">Вкл.</option>
    <option value = "0">Выкл.</option>
</select>
</td>
</tr>
</table><br>
<input type = "submit" value = "Изменить" onclick="return confirm('Вы уверены?')">

<script>
function conf() {
    if (confirm('Вы уверены?'))
    location.href = "delphoneact.php?name=<?php echo $row[1]; ?>";
    }
</script>
<input type="button" value="Удалить" onclick="javascript:conf()">

</form>

| <a href=/admin/phone>Назад</a> |

</center>
</body>
</html>

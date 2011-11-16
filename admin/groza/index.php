<html>
<head>
<title>Monitoring SouthSide .NET </title>
<style type="text/css">
body {
  color: #444;
  font: normal 95% 'Droid Sans', arial, serif;
  margin:0;
  padding:0;
  text-align: center;
}
a {
  text-decoration: none;
  color: #112046;
}
table {
	margin-left:auto; 
	margin-right:auto;
}
</style>
</head>
<body>
<h2>Гроза</h2>

| <a href=/admin>Назад</a> |<br /><br />

<table border=1 width=200px>
<tr align=center>
<td colspan=2>1-й Южный</td>
</tr>
<tr align=center>
<td><a href=r1poff.php>Выкл.</a></td><td><a href=r1pon.php>Вкл.</a></td>
</tr>
<tr align=center>
<td colspan=2>2-й Южный</td>
</tr>
<tr align=center>
<td><a href=r2poff.php>Выкл.</a></td><td><a href=r2pon.php>Вкл.</a></td>
</tr>
<tr align=center>
<td colspan=2>3-й Южный</td>
</tr>
<tr align=center>
<td><a href=r3poff.php>Выкл.</a></td><td><a href=r3pon.php>Вкл.</a></td>
</tr>
<tr align=center>
<td colspan=2>4-й Южный</td>
</tr>
<tr align=center>
<td><a href=r4poff.php>Выкл.</a></td><td><a href=r4pon.php>Вкл.</a></td>
</tr>
</table>
<a href=/admin/groza/index.php?sync><h3>Сихронизация с должниками</h3><a/>

<?php 
if(isset($_GET['sync'])) {
	system("sudo -u sysusr ssh -p 2221 195.114.30.5 /usr/bin/php /usr/local/share/phpact/portoff.php");
  system("sudo -u sysusr ssh -p 2221 195.114.30.5 /usr/bin/php /usr/local/share/phpact/portoff_groza.php");
  header("location:/admin/groza/");
  die;
}
?>

| <a href=/admin>Назад</a> |

</body>
</html>
        

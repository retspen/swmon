<html>
<head>
<title>Monitoring SouthSide .NET</title>
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
  margin-left: auto;
  margin-right: auto;
}
</style>
</head>
<body>
<a href=/><h2>Monitoring SouthSide .NET</h2></a>
<?php if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; } ?> <a href=addphone.php>Добавить</a> |

<?php

include("../../include/config.php");

$sql = "SELECT id, name, email, enable 
	FROM notif";
$res = mysql_query($sql);

$i = 1;

echo "<br /><br /><table border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr bgcolor=\"#72ff72\" align=center><td>№</td><td>Название:</td><td>Email:</td><td>Активен:</td></tr>";

while($row = mysql_fetch_row($res)){

    if($row[3] == 1)
    {
	$active = "Вкл.";
    }
    else
    {
	$active = "Выкл.";

    }

    echo "<tr bgcolor=\"#65bcff\" align=center><td>$i</td><td><a href=cngphone.php?id=$row[0]>$row[1]</a></td><td><a href=\"mailto:$row[2]\">$row[2]</a></td><td>$active</td></tr>";
    $i++;
}

echo "</table><br />";

?>

<?php if (isset($_SERVER['HTTP_REFERER'])) { echo '| <a href="'.$_SERVER['HTTP_REFERER'].'">Назад</a> |'; } ?>

</body>
</html>

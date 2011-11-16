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
  margin-left: auto;
  margin-right: auto;
}
</style>
</head>
<body>
<a href=/><h2>Monitorig SouthSide .NET</h2></a>
<h4>Добавить новое устройство</h4>
<form method = "post"
      action = "addswact.php">
<table>
<tr>
<td>Имя свитча:</td>
<td>
<input type = text
       name = "name"
       size = "16">
</td>
</tr>
<tr>
<td>IP адрес:</td>
<td>
<input type = "text" 
       name = "ip" 
       value = "172.16."
       size = "16">
</td>
</tr>
<tr>
<td>GPS точка:</td>
<td>
<input type = "text" 
       name = "point" 
       size = "16">
</td>
</tr>
<tr>
<td>GPS линк:</td>
<td>
<input type = "text" 
       name = "link" 
       size = "16">
</td>
</tr>
<tr>
<td>Модель:</td>
<td>
<select name = snmp>
    <option value = "1">ES3510</option>
    <option value = "2">ES3528M</option>
    <option value = "3">ES3552M</option>
    <option value = "4">ES3510MA</option>
</select>
</td>
</tr>
</table><br>
<input type = "submit"
       value = "Добавить">
</form>

| <a href=/admin>Назад</a> | <a href=http://api.yandex.ru/maps/tools/getlonglat/>Определение кардинат</a> |

</body>
</html>
        

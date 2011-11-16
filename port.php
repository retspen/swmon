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

$sql = "SELECT d.ip, p.name, s.name, p.port, d.id, m.snmp2 
	   FROM device d
	   LEFT JOIN ports p
	   ON d.id=p.device_id 
	   LEFT JOIN state s
	   ON p.state_id=s.id
	   LEFT JOIN snmp m
	   ON d.snmp_id=m.id
	   WHERE d.id='$id' 
	   AND p.port='$port'";
$res = mysql_query($sql);
$row = mysql_fetch_row($res);

?>

<html>
<head>
<title>Monitoring SouthSide .NET</title>
<meta http-equiv="refresh" content="180">
<meta http-equiv="expires" conten="Wed, 15 Nov 2010 09:00:57 GMT">
<meta http-equiv="Pragma" conten="no-cache">
<meta http-equiv="Cache-Control" conten="no-cache">
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
table
{
	margin-left:auto; 
	margin-right:auto;
}
</style>
</head>
</body>
<h2>Управление портом №<?php echo $row[3]; ?></h2>
<a href=telnet://<?php echo $row[0]; ?>><font color="blue"><b><?php echo $row[1]; ?></font></b></a>

<?php 

/* Показать статус порта */
$prst = snmpget("$row[0]","private","ifOperStatus.$port");

if(ereg("Down",$prst) || ereg("down",$prst))
{
    echo "<font color=\"red\"><h5>Не активен</h5></font>\n"; 
}
else
{
    echo "<font color=\"green\"><h5>Активен</h5></font>\n";
}
?>

<?php
/* Выключение и включение порта */
if($row[2] == DOWN)
{
    print "<a href=\"port.php?id=$id&port=$port&porton\"><b>[Включить порт]</b></a>";
}
else
{
    print "<a href=\"port.php?id=$id&port=$port&portoff\"><b>[Выключить порт]</b></a>";
}

echo "<br /><br />";

/* IPTV */
$iptv = snmpget("$row[0]","public","$row[5].$row[3]");

if(ereg("2", $iptv) || ereg("1", $iptv)) {
        print "<a href=\"port.php?id=$id&port=$port&iptvoff\"><b>[Выключить IPTV]</b></a>";
} else {
        print "<a href=\"port.php?id=$id&port=$port&iptvon\"><b>[Включить IPTV]</b></a>";
}

/* Actions */
if(isset($_GET['porton'])) {
    snmpset("$row[0]","private","ifAdminStatus.$port","i","1");
    $sqlup = "UPDATE ports SET state_id=1 WHERE port='$port' AND device_id='$id'";
    $resup = mysql_query($sqlup,$dbconnect);
    sleep(2);
    header("location:port.php?id=$id&port=$port");
    die();
} else if(isset($_GET['portoff'])) {
    snmpset("$row[0]","private","ifAdminStatus.$port","i","2");
    $sqldown = "UPDATE ports SET state_id=2 WHERE port='$port' AND device_id='$id'";
    $resdown = mysql_query($sqldown,$dbconnect);
    header("location:port.php?id=$id&port=$port");
    die();
} else if(isset($_GET['iptvon'])) {
    snmpset("$row[0]","private","$row[5].$row[3]","i","2");
    header("location:port.php?id=$id&port=$port");
    die();
} else if(isset($_GET['iptvoff'])) {
    snmpset("$row[0]","private","$row[5].$row[3]","i","0");
    header("location:port.php?id=$id&port=$port");
    die();
} else if(isset($_GET['graf'])) {
    /* Преобразование в UNIXTIMESTAMP */
    $start = mktime($shr, $smin, 00, $smon, $sday, $syear);
    $end = mktime($ehr, $emin, 00, $emon, $eday, $eyear);

    $rrd = array( "--start=$start", "--end=$end",
                "DEF:in=$homedir/rrd/".$id."_".$port.".rrd:in:AVERAGE",
                "DEF:out=$homedir/rrd/".$id."_".$port.".rrd:out:AVERAGE",
                "CDEF:inbits=in,8,*",
                "CDEF:outbits=out,8,*",
                "AREA:inbits#00CF00FF:In",
                "GPRINT:inbits:LAST: Current\:%8.2lf %s",
                "GPRINT:inbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:inbits:MAX:Max\:%8.2lf %s\\n",
                "LINE1:outbits#002A97FF:Out",
                "GPRINT:outbits:LAST:Current\:%8.2lf %s",
                "GPRINT:outbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:outbits:MAX:Max\:%8.2lf %s",
                );

    $res = rrd_graph("$homedir/tmp/".$id."_".$port.".png", $rrd, count($rrd));

    header("location:port.php?id=$id&port=$port");
    die();
}
?>

<br />
<br />
| <a href=/>Главная</a> | <a href=/yamaps.php>Карта</a> | <a href=/logport.php?id=<?php echo "$row[4]";?>&port=<?php echo "$row[3]";?>>Лог</a> | <a href=/mac.php?id=<?php echo "$row[4]";?>&port=<?php echo "$row[3]";?>>MAC</a> |
<hr />
<h3>История трафика</h3>
<form method="post" action="port.php?id=<?php echo "$id"; ?>&port=<?php echo "$port"; ?>&graf">
<b>Начальная дата:</b>
<br>
Время:
<select name="shr">
<?php
    $h = date(H);
    echo "<option value='$h' selected>$h</option>";
    for ($i = 0; $i < 24  ; $i++)
    {
         echo "<option value='$i'>$i</option>";
    }
?>
</select>
:
<select name="smin">
<?php
    $min = date(i);
    echo "<option value='$min' selected>$min</option>";
    for ($i = 0; $i < 60  ; $i++)
    {
         echo "<option value='$i'>$i</option>";
    }
?>
</select>
Дата:
<select name="sday">
<?php
    $d = date(d);
    echo "<option value='$d' selected>$d</option>";

    for ($i = 1; $i < 32  ; $i++)
    {
         echo "<option value='$i'>$i</option>";
    }
?>
</select>
/
<select name="smon">
<?php
    $m = date(m);
    echo "<option value='$m' selected>Текущий</option>";
    echo "<option value=\"1\">Январь</option>";
    echo "<option value=\"2\">Февраль</option>";
    echo "<option value=\"3\">Март</option>";
    echo "<option value=\"4\">Апрель</option>";
    echo "<option value=\"5\">Май</option>";
    echo "<option value=\"6\">Июнь</option>";
    echo "<option value=\"7\">Июль</option>";
    echo "<option value=\"8\">Август</option>";
    echo "<option value=\"9\">Сентябрь</option>";
    echo "<option value=\"10\">Октябрь</option>";
    echo "<option value=\"11\">Ноябрь</option>";
    echo "<option value=\"12\">Декабрь</option>";
?>
</select>
/
<select name="syear">
<?php
    $y = date(Y);
    echo "<option value='$y' selected>$y</option>";
    for ($i = 2010; $i < date("Y") +1; $i++)
    {
        echo "<option value='$i'>$i</option>";
    }
?>
</select>
<br>
<b>Конечная дата:</b>
<br>
Время:
<select name="ehr">
<?php
    $h = date(H);
    echo "<option value='$h' selected>$h</option>";
    for ($i = 0; $i < 24  ; $i++)
    {
         echo "<option value='$i'>$i</option>";
    }
?>
</select>
:
<select name="emin">
<?php
    $min = date(i);
    echo "<option value='$min' selected>$min</option>";
    for ($i = 0; $i < 60  ; $i++)
    {
         echo "<option value='$i'>$i</option>";
    }
?>
</select>
Дата:
<select name="eday">
<?php
    $d = date(d);
    echo "<option value='$d' selected>$d</option>";

    for ($i = 1; $i < 32  ; $i++)
    {
         echo "<option value='$i'>$i</option>";
    }
?>
</select>
/
<select name="emon">
<?php
    $m = date(m);
    echo "<option value='$m' selected>Текущий</option>";
    echo "<option value=\"1\">Январь</option>";
    echo "<option value=\"2\">Февраль</option>";
    echo "<option value=\"3\">Март</option>";
    echo "<option value=\"4\">Апрель</option>";
    echo "<option value=\"5\">Май</option>";
    echo "<option value=\"6\">Июнь</option>";
    echo "<option value=\"7\">Июль</option>";
    echo "<option value=\"8\">Август</option>";
    echo "<option value=\"9\">Сентябрь</option>";
    echo "<option value=\"10\">Октябрь</option>";
    echo "<option value=\"11\">Ноябрь</option>";
    echo "<option value=\"12\">Декабрь</option>";
?>
</select>
/
<select name="eyear">
<?php
    $y = date(Y);
    echo "<option value='$y' selected>$y</option>";
    for ($i = 2010; $i < date("Y") +1; $i++)
    {
        echo "<option value='$i'>$i</option>";
    }
?>
</select>
<br>
<br>
<input type = "submit"
       value = "Сформировать">
</form>

<?php
    /* HTML */
    print "<h4>За выбранный период</h4>";

    /* Проверка сущетсвует ли файл */
    if (@fopen("tmp/".$id."_".$port.".png", "r")) 
    {
	print "<img src=tmp/".$id."_".$port.".png>";
    }
    else
    {
	echo "<font color=grey>Отчет не сформирован</font>";
    }
    print "<br />";

/* Вывод графиков загрузки порта */
/* За час */
$rrh = array( "--start", "-1h", "--title=$row[1]",
                "DEF:in=$homedir/rrd/".$id."_".$port.".rrd:in:AVERAGE",
		"DEF:out=$homedir/rrd/".$id."_".$port.".rrd:out:AVERAGE",
                "CDEF:inbits=in,8,*",
                "CDEF:outbits=out,8,*",
                "AREA:inbits#00CF00FF:In",
                "GPRINT:inbits:LAST: Current\:%8.2lf %s",
                "GPRINT:inbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:inbits:MAX:Max\:%8.2lf %s\\n",
                "LINE1:outbits#002A97FF:Out",
                "GPRINT:outbits:LAST:Current\:%8.2lf %s",
                "GPRINT:outbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:outbits:MAX:Max\:%8.2lf %s",   
		);

$resh = rrd_graph("$homedir/tmp/".$id."_".$port."_1h.png", $rrh, count($rrh));
/* За день */
$rrd = array( "--start", "-1d", "--title=$row[1]",
	        "DEF:in=$homedir/rrd/".$id."_".$port.".rrd:in:AVERAGE",
                "DEF:out=$homedir/rrd/".$id."_".$port.".rrd:out:AVERAGE",
                "CDEF:inbits=in,8,*",
                "CDEF:outbits=out,8,*",
                "AREA:inbits#00CF00FF:In",
                "GPRINT:inbits:LAST: Current\:%8.2lf %s",
                "GPRINT:inbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:inbits:MAX:Max\:%8.2lf %s\\n",
                "LINE1:outbits#002A97FF:Out",
                "GPRINT:outbits:LAST:Current\:%8.2lf %s",
                "GPRINT:outbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:outbits:MAX:Max\:%8.2lf %s",   
		);

$resd = rrd_graph("$homedir/tmp/".$id."_".$port."_1d.png", $rrd, count($rrd));
/* За неделю */
$rrw = array( "--start", "-1w", "--title=$row[1]",
                "DEF:in=$homedir/rrd/".$id."_".$port.".rrd:in:AVERAGE",
                "DEF:out=$homedir/rrd/".$id."_".$port.".rrd:out:AVERAGE",
                "CDEF:inbits=in,8,*",
                "CDEF:outbits=out,8,*",
                "AREA:inbits#00CF00FF:In",
                "GPRINT:inbits:LAST: Current\:%8.2lf %s",
                "GPRINT:inbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:inbits:MAX:Max\:%8.2lf %s\\n",
                "LINE1:outbits#002A97FF:Out",
                "GPRINT:outbits:LAST:Current\:%8.2lf %s",
                "GPRINT:outbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:outbits:MAX:Max\:%8.2lf %s",   
		);

$resw = rrd_graph("$homedir/tmp/".$id."_".$port."_1w.png", $rrw, count($rrw));
/* За месяц */
$rrm = array( "--start", "-1m", "--title=$row[1]",
                "DEF:in=$homedir/rrd/".$id."_".$port.".rrd:in:AVERAGE",
                "DEF:out=$homedir/rrd/".$id."_".$port.".rrd:out:AVERAGE",
                "CDEF:inbits=in,8,*",
                "CDEF:outbits=out,8,*",
                "AREA:inbits#00CF00FF:In",
                "GPRINT:inbits:LAST: Current\:%8.2lf %s",
                "GPRINT:inbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:inbits:MAX:Max\:%8.2lf %s\\n",
                "LINE1:outbits#002A97FF:Out",
                "GPRINT:outbits:LAST:Current\:%8.2lf %s",
                "GPRINT:outbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:outbits:MAX:Max\:%8.2lf %s",   
		);

$resm = rrd_graph("$homedir/tmp/".$id."_".$port."_1m.png", $rrm, count($rrm));
/* За год */
$rry = array( "--start", "-1y", "--title=$row[1]",
                "DEF:in=$homedir/rrd/".$id."_".$port.".rrd:in:AVERAGE",
                "DEF:out=$homedir/rrd/".$id."_".$port.".rrd:out:AVERAGE",
                "CDEF:inbits=in,8,*",
                "CDEF:outbits=out,8,*",
                "AREA:inbits#00CF00FF:In",
                "GPRINT:inbits:LAST: Current\:%8.2lf %s",
                "GPRINT:inbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:inbits:MAX:Max\:%8.2lf %s\\n",
                "LINE1:outbits#002A97FF:Out",
                "GPRINT:outbits:LAST:Current\:%8.2lf %s",
                "GPRINT:outbits:AVERAGE:Average\:%8.2lf %s",
                "GPRINT:outbits:MAX:Max\:%8.2lf %s",   
		);

$resy = rrd_graph("$homedir/tmp/".$id."_".$port."_1y.png", $rry, count($rry));

/* Вывод графиков */
print "<h4>За час</h4>";
print "<img src=tmp/".$id."_".$port."_1h.png>";
print "<h4>За День</h4>";
print "<img src=tmp/".$id."_".$port."_1d.png>";
print "<h4>За Неделю</h4>";
print "<img src=tmp/".$id."_".$port."_1w.png>";
print "<h4>За Месяц</h4>";
print "<img src=tmp/".$id."_".$port."_1m.png>";
print "<h4>За Год</h4>";
print "<img src=tmp/".$id."_".$port."_1y.png>";
print "<br />";
print "<br />";
print "| <a href=/yamaps.php>Карта</a> | <a href=/>Полотенце</a> |<br /><br />";
?>
</body>
</html>

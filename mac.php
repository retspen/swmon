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
<center><h2>MAC порта №<?php echo $port; ?></h2>

<?php 

$sql = "SELECT d.ip, s.snmp1, s.snmp3, s.snmp4, s.snmp5, s.snmp6, s.snmp7
	   FROM device d
	   LEFT JOIN snmp s
	   ON d.snmp_id=s.id
	   WHERE d.id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_row($res);

echo "| <a href=/>Главная</a> | <a href=/port.php?id=$id&port=$port>Назад</a> | <a href=telnet://$row[0]>Telnet</a> | <a onclick=\"window.open('http://dhcp.ss.zp.ua/','TIP','width=850,height=650,status=0,menubar=0,location=0,resizable=0,directories=0,toolbar=0,scrollbar=0');return false;\" href=\"http://dhcp.ss.zp.ua/\">DHCP</a> | <br />";

/* Выключение и включение порта */
echo "<font color=green><h4>Статус:</h4></font>";

$prst = snmpget("$row[0]","private","ifOperStatus.$port");

if(ereg("Down",$prst) or ereg("down",$prst))
{
    print "<a href=\"mac.php?id=$id&port=$port&porton\"><b>[Включить порт]</b></a>";
}
else
{
    print "<a href=\"mac.php?id=$id&port=$port&portoff\"><b>[Выключить порт]</b></a>";
}

/* Description */
echo "<font color=green><h4>Название:</h4></font>";

$getname = snmpget("$row[0]", "public", "$row[1].$port");
$strname = substr($getname, 9, strlen($getname)-10);
if($strname != NULL) {
?>
     <form method = "post" action = "mac.php?id=<?php echo $id; ?>&port=<?php echo $port; ?>&descn">
          <input type = text
                 name = "name"
                 value = "<?php echo "$strname\n"; ?>"
                 size = "18">
         <br /><br />
         <input type = "submit"
                value = "Изменить">
         <input type="button" value="Очистить" onclick="location.href='mac.php?id=<?php echo $id; ?>&port=<?php echo $port; ?>&desclear'" />
    </form>
    <?php
} else {
?>
     <form method = "post" action = "mac.php?id=<?php echo $id; ?>&port=<?php echo $port; ?>&descn">
          <input type = text
                 name = "name"
                 size = "18">
         <br /><br />
         <input type = "submit"
                value = "Изменить">
    </form>
<?php
}

/* Erros In or OUT */
//in
$getinerr = snmpget("$row[0]", "public", ".1.3.6.1.2.1.2.2.1.14.$port");
$strinerr = substr($getinerr, 10, strlen($getinerr)-10);
//out
$getouterr = snmpget("$row[0]", "public", ".1.3.6.1.2.1.2.2.1.20.$port");
$strouterr = substr($getouterr, 10, strlen($getouterr)-10);

?>
<table border="0" cellpadding="5" cellspacing="0">
<tr><th colspan="2"><?php echo "<font color=green>Ошибки:</font>"; ?></th></tr>
<tr>
<td><?php echo "<i>In:</i> $strinerr"; ?></td>
<td><?php echo "<i>Out:</i> $strouterr"; ?></td>
</tr>
</table>
<?php

?>
<form method = "post" action = "mac.php?id=<?php echo $id; ?>&port=<?php echo $port; ?>&errclear">
           <input type = "submit"
                  value = "Очистить">
</form>
<?php

/* VLAN  */
//echo "<h4>VLAN порта:</h4>";

$vlan = array(101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 114, 115, 172, 173, 184, 185);

for($y=0; $y <= count($vlan)-1; $y++) {
    	$numvlan = snmpget("$row[0]", "public", "$row[2].$vlan[$y]");

	if(ereg("1",$numvlan)) {
	    $vl = $vlan[$y];
	    //echo "VLAN: $vl\n";
	}
}

/* MAC */
echo "<font color=green><h4>MAC-адрес:</h4></font>\n";

$getprt = snmpwalk("$row[0]","public",".1.3.6.1.2.1.17.4.3.1.2");
$getmac = snmpwalk("$row[0]","public",".1.3.6.1.2.1.17.4.3.1.1");

for($i=0; $i<=count($getprt)-1; $i++) {
    $prt = substr($getprt[$i], 9, strlen($getprt[$i])-9);
        if($prt == $port){
        	$strmac = substr($getmac[$i], 12, 17);
        	$mac = str_replace(' ',':',$strmac);
    		echo "$mac<br />\n";
        }
}

/* port security max-mac-coumnt */
echo "<font color=green><h4>Контроль MAC:</h4></font>\n";

$maxcount = snmpget("$row[0]","public","$row[3].$port");

if(ereg("1", $maxcount) || ereg("2", $maxcount)) {
	print "<a href=\"mac.php?id=$id&port=$port&maxoff\"><b>[2.Очистить]</b></a>";
} else {
	print "<a href=\"mac.php?id=$id&port=$port&maxon\"><b>[Включить] </b></a>";
}

/* ip source guard sip-mac */
echo "<br />";
echo "<font color=green><h4>Привязка MAC:</h4></font>\n";

$sipmac = snmpget("$row[0]","public","$row[4].$port");

if(ereg("2", $sipmac)) {
	print "<a href=\"mac.php?id=$id&port=$port&sipmacoff\"><b>[Выключить]</b></a>";
} else {
	print "<a href=\"mac.php?id=$id&port=$port&sipmacon\"><b>[Включить]</b></a>";
}

/* Source Guard  */
echo "<font color=green><h4>Привязка по IP:</h4></font>\n";

/* Достаем MAC в нормально для нас виде */
list($mac_1,$mac_2,$mac_3,$mac_4,$mac_5,$mac_6) = explode(':', $mac);
$data=array(hexdec($mac_1),hexdec($mac_2),hexdec($mac_3),hexdec($mac_4),hexdec($mac_5),hexdec($mac_6));
$decmac = implode($data,".");

$ipsog = snmpget("$row[0]","public","$row[5].$vl.$decmac");
$stripsog = substr($ipsog, 11, strlen($ipsog)-11);

$iptype = snmpget("$row[0]","public","$row[6].$vl.$decmac");

if(ereg("3",$iptype)) {
	echo "<a onclick=\"window.open('http://dhcp.ss.zp.ua/logdhcp.php?show_log=".$stripsog."','TIP','width=850,height=650,status=0,menubar=0,location=0,resizable=0,directories=0,toolbar=0,scrollbar=0');return false;\" href=\"http://dhcp.ss.zp.ua/\">$stripsog</a>\n";
    ?>
    <form method = "post" action = "mac.php?id=<?php echo $id; ?>&port=<?php echo $port; ?>&ipsoclear">
               <br />
               <input type = "submit"
                      value = "1.Удалить">
     </form>
     <font color=red>Для удаления MAC последовательность с 1 по 2</font><br /><br />
    <?php
} else {
    ?>
    <form method = "post" action = "mac.php?id=<?php echo $id; ?>&port=<?php echo $port; ?>&ipsoadd">
	<input type = text
	       name = "ip"
               value = "94.240.1"
               size = "13">
               <br /><br />
               <input type = "submit"
                      value = "Добавить">
     </form>
    <?php
}

?>
<form method = "post" action = "mac.php?id=<?php echo $id; ?>&port=<?php echo $port; ?>&save">
	<input type = "submit" value = "Сохранить">
</form>
<?php

/* Actions */
if(isset($_GET['maxoff'])) {
    snmpset("$row[0]","private","$row[3].$port","i","0");
    snmpset("$row[0]","private","ifAdminStatus.$port","i","2");
    snmpset("$row[0]","private","ifAdminStatus.$port","i","1");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['maxon'])) {
    snmpset("$row[0]","private","$row[3].$port","i","1");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['sipmacon'])) {
    snmpset("$row[0]","private","$row[4].$port","i","2");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['sipmacoff'])) {
    snmpset("$row[0]","private","$row[4].$port","i","0");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['desclear'])) {
    snmpset("$row[0]","private","$row[1].$port","s","");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['descn'])) {
    snmpset("$row[0]","private","$row[1].$port","s","$name");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['ipsoadd'])) {
    $nmac = str_replace(':','-',$mac);
    system("$homedir/bin/ipadd.pl $row[0] $nmac $vl $ip $port");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['ipsoclear'])) {
    $nmac = str_replace(':','-',$mac);
    system("$homedir/bin/ipdel.pl $row[0] $nmac $vl");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['errclear'])) {
    system("$homedir/bin/clear.pl $row[0] $port");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['save'])) {
    system("$homedir/bin/save.pl $row[0]");

    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['porton'])) {
    snmpset("$row[0]","private","ifAdminStatus.$port","i","1");
    sleep(3);
    header("location:mac.php?id=$id&port=$port");
    die();
} else if(isset($_GET['portoff'])) {
    snmpset("$row[0]","private","ifAdminStatus.$port","i","2");
    header("location:mac.php?id=$id&port=$port");
    die();
}

echo "| <a href=/port.php?id=$id&port=$port>Назад</a> |"; 
?>
</center>
</body>
</html>

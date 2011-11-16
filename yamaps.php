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

date_default_timezone_set('UTC');

/* Настройка Яндекс Карт */
$ymap .= "
<html>
<head>
<title>Monitoring SouthSide .NET</title>
<style type=\"text/css\">
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
<meta http-equiv='refresh' content='600'>
<script src='http://api-maps.yandex.ru/1.1/index.xml?key=ALuyCU4BAAAAX0sNdAMAsqWWubdKRO2C-L_t9hknw-HGyykAAAAAAAAAAAAchwWmuCYR5Y-IxSsQOQldoW1diA==' type='text/javascript'></script>
     <script type='text/javascript'>   
        YMaps.jQuery(function () { 
            var map = new YMaps.Map(YMaps.jQuery('#YMapsID')[0]);
            map.setCenter(new YMaps.GeoPoint(35.183395,47.778391), 15, YMaps.MapType.HYBRID);

            map.addControl(new YMaps.ToolBar());
            map.enableScrollZoom();
            var zoom = new YMaps.Zoom({
                customTips: [
	            { index: 17, value: \"Крупнее\" }
                    ]
            });
	    map.addControl(zoom);

            var typeControl = new YMaps.TypeControl([YMaps.MapType.HYBRID, YMaps.MapType.MAP],[0,1]);
            map.addControl(typeControl);

            var toolbar = new YMaps.ToolBar();

            var button = new YMaps.ToolBarButton({ 
        	caption: \"<a href='/'>Главная</a>\",
                hint: \"Перейти на главную\"
            });

            toolbar.add(button);
            map.addControl(toolbar);

            var up = new YMaps.Style();
            up.iconStyle = new YMaps.IconStyle();
            up.iconStyle.href = '/icon/up.png';
            up.iconStyle.size = new YMaps.Point(20, 20);
            up.iconStyle.offset = new YMaps.Point(-10, -10);

            var down = new YMaps.Style();
            down.iconStyle = new YMaps.IconStyle();
            down.iconStyle.href = '/icon/down.gif';
            down.iconStyle.size = new YMaps.Point(20, 20);
            down.iconStyle.offset = new YMaps.Point(-10, -10);

            var s = new YMaps.Style();
            s.lineStyle = new YMaps.LineStyle();
            s.lineStyle.strokeColor = \"d90000\";
            s.lineStyle.strokeWidth = \"3\";
            YMaps.Styles.add(\"CustomLine\", s);

            var s = new YMaps.Style();
            s.lineStyle = new YMaps.LineStyle();
            s.lineStyle.strokeColor = \"8a6089\";
            s.lineStyle.strokeWidth = \"3\";
            YMaps.Styles.add(\"CustomLineOFF\", s);
            ";

/* Вывести полный список точек */
$sql = "SELECT d.id, d.name, d.ip, g.point, d.state_id, s.ports, g.link, s.snmp8
	FROM device d
	LEFT JOIN geo g
	ON d.id = g.device_id
	LEFT JOIN snmp s
	ON d.snmp_id = s.id";
$alldev = mysql_query($sql);

/* Вывести полный список свичей */
while($rowsql = mysql_fetch_row($alldev)){
    /* Если вклчючен то показывать точку зеленым */
    if($rowsql[4] == 1) 
    {
	$sql = "SELECT p.port, p.name, p.mac, s.name, p.trank
		FROM device d
		LEFT JOIN ports p ON d.id=p.device_id
		LEFT JOIN state s ON p.state_id=s.id
		WHERE d.id=$rowsql[0]
		ORDER BY p.port+0";
	$show_sw = mysql_query($sql);

	while($port_row = mysql_fetch_row($show_sw)){
			/* Если сожержит одно из перечисленных то красить строку в указанный цвет */
			if($port_row[4] == 1 or $port_row[4] == 2)
			{
				$prst = snmpget("$rowsql[2]","private","ifOperStatus.$port_row[0]");
				if(ereg("Down",$prst)) {
					$tr = "tr style=\"font-size: 70%; background: #ff6060\"";
					$td = "<a href=trank.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
				} else {
					$tr = "tr style=\"font-size: 70%; background: #50ff50\"";
					$td = "<a href=trank.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
				}
			}
			else if(ereg("FAIL",$port_row[1]))
			{
				$tr = "tr style=\"font-size: 70%; background: #d6d6ff\"";
				$td = "$port_row[1]";
			}
			else if(ereg("_rezerv",$port_row[1]))
			{
				$tr = "tr style=\"font-size: 70%; background: #d6d6ff\"";
				$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
			}
			else if(ereg("_off",$port_row[1]))
			{
				$tr = "tr style=\"font-size: 70%; background: #4a4a4a\"";
				$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
			}
			else if($port_row[3] == UP)
			{
				$tr = "tr style=\"font-size: 70%; background: #c5f1c5\"";
				$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
			}
			else
			{
				$tr = "tr style=\"font-size: 70%; background: #ccc\"";
				$td = "<a href=port.php?id=$rowsql[0]&port=$port_row[0]>$port_row[1]</a>";
			}

                        if($port_row[3] == "DOWN"){
                            $port_state = "D";
                        }else{
                            $port_state = "U";
                        }

			/* Построение таблицы  */
			$table = "<$tr><td>$port_row[0]</td><td>$td</td><td align=\"center\"><a href=mac.php?id=$rowsql[0]&port=$port_row[0]>$port_row[2]</a></td><td align=\"center\">$port_state</td></tr>";

			/* Преобразование из цикла в строку */
			if(!end($table)){
			    $array_row_port .= $table.' ';
			}
		}

		/* Получение времени жизни кольца */
		if($rowsql[5] == 10) {
		    $mst = snmpget("$rowsql[2]","public","$rowsql[7]");
		    $strmst = substr($mst, 8);
		    $sec = $strmst / 100;
		    $time = date('H:i', $sec);
		    $day = date('d', $sec)-1;
		} else {
		    $mst = snmpget("$rowsql[2]","public","$rowsql[7]");
		    $strmst = substr($mst,12,strpos($mst,')')-12);
		    $sec = $strmst / 100;
		    $time = date('H:i', $sec);
		    $day = date('d', $sec)-1;
		}

		/* Построение точки на карте */
	$ymap .= "
	    var placemark = new YMaps.Placemark(new YMaps.GeoPoint($rowsql[3]), {hasHint: 1, style: up});
	    placemark.name = '<span style=\"background-color: #b0b0ff; color: #000\"><a href=telnet://$rowsql[2]>$rowsql[1]</a> MST: $day d, $time</span>';
	    map.addOverlay(placemark);
	    placemark.description = '<table border=0 cellspacing=1 cellpadding=0 style=\"background: #fff\"><tr><td width=18px></td><td width=120px></td><td width=20px></td><td width=20px></td></tr>$array_row_port</table>';
            ";
		/* Очишаем переменную для следующего круга */
		$array_row_port = "";
	
	$ymap .= "
	    var polyline = new YMaps.Polyline([
            new YMaps.GeoPoint($rowsql[3]),
            new YMaps.GeoPoint($rowsql[6])], {style: \"CustomLine\"});
            map.addOverlay(polyline);
            ";
    /* Если выклчючен то показывать точку красным */
    } else {
	$ymap .= "
            var placemark = new YMaps.Placemark(new YMaps.GeoPoint($rowsql[3]), {style: down});
            placemark.name = '<a href=telnet://$rowsql[2]>$rowsql[1]</a>';
            map.addOverlay(placemark);
            placemark.description = '<h3>Свитч недоступен!</h3>';
            ";

    	$ymap .= "
	    var polyline = new YMaps.Polyline([
            new YMaps.GeoPoint($rowsql[3]),
            new YMaps.GeoPoint($rowsql[6])], {style: \"CustomLineOFF\"});
            map.addOverlay(polyline);
            ";
    }
}

/* Яндек карта */
$ymap .= "
   })
    </script>
</head>
<body>
<div id=\"YMapsID\" style=\"width:100%;height:100%\"></div> 
</body>
</html>";

/* Вывести карту */
echo $ymap;

?>

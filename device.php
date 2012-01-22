<?PHP
/*
	part-db version 0.1
	Copyright (C) 2005 Christoph Lechner
	http://www.cl-projects.de/

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

	$Id: $
*/
	include ("lib.php");
	partdb_init();
	
	$confirmdelete = 0;
	
	if(strcmp($_REQUEST["action"], "createdevice") == 0)  //add a new device
	{
		$query = "INSERT INTO devices (name) VALUES (". smart_escape($_REQUEST["newdevicename"]) .");";
		debug_print ($query);
		$r = mysql_query ($query);
	}
	else if(strcmp($_REQUEST["action"], "confirmeddelete") == 0)
	{
		$query = "DELETE FROM devices WHERE id=". smart_escape($_REQUEST["deviceid"]). " LIMIT 1;";
		debug_print ($query);
		$r = mysql_query ($query);
		if($r == 0)
			print "Fehler";
		$query = "DELETE FROM part_device WHERE id_device=". smart_escape($_REQUEST["deviceid"]). ";";
		debug_print ($query);
		$r = mysql_query ($query);
		if($r == 0)
			print "Fehler";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Footprints</title>
    <link rel="StyleSheet" href="css/partdb.css" type="text/css">
</head>
<body class="body">

<table class="table">
	<tr>
		<td class="tdtop">
		Neues Ger�t erzeugen
		</td>
	</tr>
	<tr>
		<td class="tdtext">
			<form method="post" action="">
			Ger�tenamen
			<input type="text" name="newdevicename" size="10" maxlength="50" >
			<input type="hidden" name="action" value="createdevice">
			<input type="submit" value="OK">
			</form>	
		</td>
	</tr>
</table>

<?php
if(strcmp($_REQUEST["action"], "deletedevice") == 0)
{
	print "<br>";
	print "<table class=\"table\">";
	print "<tr><td class=\"tdtop\">Ger�t \"".$_REQUEST["devicename"]."\" wirklich l�schen?</td></tr>";
	print "<tr><td class=\"tdtext\">";
	print "<form method=\"post\" action=\"\">";
			print "<input type=\"hidden\" name=\"action\"  value=\"confirmeddelete\"/>";
			print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
			print "<input type=\"submit\" style=\"height: 1.5em; width: 5em\" value=\"Ja\">";
	print "</form>";
	print "<form method=\"post\" action=\"\">";
			print "<input type=\"hidden\" name=\"action\"  value=\"\"/>";
			print "<input type=\"hidden\" name=\"deviceid\" value=\"" . $_REQUEST["deviceid"]. "\"/>";
			print "<input type=\"submit\" style=\"height: 1.5em; width: 5em\" value=\"Nein\">";
	print "</form></td>";
	print "</tr></table>";
}
?>
<br>
	
<table class="table">
	<tr>
		<td class="tdtop">
		Ger�te
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<table >

		<?PHP
		$query = "SELECT devices.id, devices.name, SUM(part_device.quantity), COUNT(part_device.quantity) ".
		"FROM devices LEFT JOIN part_device ".
		"ON (devices.id =  part_device.id_device) GROUP BY part_device.id_device ORDER BY devices.name ASC;";
		debug_print($query);
		$result = mysql_query ($query);
		debug_print($result);
	
		$rowcount = 0;	// $rowcount is used for the alternating bg colors
		
		print "<tr class=\"trcat\"><td>Name</td><td>Anzahl Teile</td><td>Anzahl Einzelteile</td><td>L�schen</td></tr>\n";
		
		while ( $d = mysql_fetch_row ($result) )
		{
		
		// the alternating background colors are created here
		$rowcount++;
		if ( ($rowcount & 1) == 0 )
			print "<tr class=\"trlist1\">";
		else
			print "<tr class=\"trlist2\">";
		
		print "<td class=\"tdrow1\"><a href=\"deviceinfo.php?deviceid=". smart_unescape($d[0]) ."\">". smart_unescape($d[1]) . "</a></td>\n";
		print "<td class=\"tdrow2\">". smart_unescape($d[2]) ."</td>\n";
		print "<td class=\"tdrow3\">". smart_unescape($d[3]) ."</td>\n";
		print "<td class=\"tdrow3\">";
		
		print "<form method=\"post\" action=\"\">";
		print "<input type=\"hidden\" name=\"action\"  value=\"deletedevice\"/>";
		print "<input type=\"hidden\" name=\"deviceid\" value=\"" . smart_unescape($d[0]). "\"/>";
		print "<input type=\"hidden\" name=\"devicename\" value=\"" . smart_unescape($d[1]). "\"/>";
		print "<input type=\"submit\" value=\"L�schen\"/></form>";
		
		print "</td>";
		print "</tr>\n";
		}
		?>
		</table>
		</td>
	</tr>
</table>

</body>
</html>

<html>
 <body class="body">
  <head>
   <link rel="StyleSheet" href="../css/partdb.css" type="text/css" />

<table class="table">
	<tr>
		<td class="tdtop">
		Footprints
		</td>
	</tr>
	<tr>
		<td class="tdtext">
		<table>
			</td>
			<?php
			  $pic = array();
			  $path = "footprints/";
			    $verzeichnis = @opendir($path);
			    if(!$verzeichnis) die("Kann Verzeichnis $path nicht öffnen");
			      rewinddir($verzeichnis);
			    while($file = readdir($verzeichnis)) {
			      if($file != "." and $file != ".." and $file != ".db" and $file != ".svn") {
				array_push($pic, "$file");
			      }
			    }
			    sort($pic);
			    for($x=0;$x<count($pic);$x++) {
			      $file = $pic[$x]; 
			      $title = $pic[$x];
			      // Normal
			      echo "<img src=".$path. "" .$file." title=".$title." height=\"90\"></img>";
			      // With Java Popup
			      #echo "<img src=\"" .$path. "" .$file. "\" height=\"70\" onClick=\"window.open('" .$path. "" .$file. "','320','240','menubar=no')\">";
			    }
			  ?>
			</tr>
		</table>
		</td>
	</tr>
  </table>

  </head>
 </body>
</html>
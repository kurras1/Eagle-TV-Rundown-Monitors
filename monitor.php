<?php
//ETV Rundown Applet - monitor.php
//Programmer - Michael Kurras

//The "Display" side
?>
<!DOCTYPE html>
<html>

<head>
<link href="css/monitor.css" rel="stylesheet">

</head>
<body onload="updateRundown()">
<h1>SHOW RUNDOWN</h1>
<table id="rundown-table">
<tr>
<td><h1>1</h1></td>
<td class="rundown-default"><h2 >Segment Title<h2></td>
<td class="rundown-default"><h2 >TRT</h2></td>
</tr>

</table>
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/rundown-process.js"></script>
</body>

</html>
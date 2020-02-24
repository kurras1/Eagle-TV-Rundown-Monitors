<?php
//ETV Rundown Applet - request.php
//Programmer - Michael Kurras

//Returns an XML file of the rundown

header('Content-type: text/xml');
echo '<rundown>';
include 'config.php';

$query = "SELECT `value` FROM `datastore` WHERE `key` = 'updated' LIMIT 1;";

$result = $conn->query($query);
$value = mysqli_fetch_row($result);

echo '<updated>';
	echo  $value[0];
echo '</updated>';

echo '<start-time>';

echo '</start-time>';

$query = "SELECT uid, title, producer, runtime, audio, type, description, rundown_pos FROM rundown ORDER BY rundown_pos ASC";

$result = $conn->query($query);

while($row = $result->fetch_assoc())
{
	echo '<segment>';
	
	echo '<uid>';
	echo $row['uid'];
	echo '</uid>';
	
	echo '<title>';
	echo $row['title'];
	echo '</title>';
	
	echo '<producer>';
	echo $row['producer'];
	echo '</producer>';
	
	echo '<runtime>';
	echo $row['runtime'];
	echo '</runtime>';
	
	echo '<trt>';
	if($row['runtime'] >= 0){
	echo gmdate("i:s", $row['runtime']);
	} else if ($row['runtime'] == -1){
		echo "LIVE";
	}
	echo '</trt>';
	
	echo '<audio>';
	switch ($row['audio']) {
		case 0:
			echo "SOT";
			break;
		case 1:
			echo "NAT";
			break;
		case 2:
			echo "NAT+MUSIC";
			break;
		case 3:
			echo "SILENT";
			break;
	}
	echo '</audio>';
	
	echo '<type>';
	switch ($row['type']) {
		case 0:
			echo "Segment";
			break;
		case 1:
			echo "B-Roll";
			break;
		case 2:
			echo "Intro";
			break;
		case 3:
			echo "Studio Set";
			break;		
		case 4:
			echo "Greenscreen";
			break;		
		case 5:
			echo "Graphic / DVE";
			break;	
	}
	echo '</type>';
	
	echo '<description>';
	echo $row['description'];
	echo '</description>';
	
	echo '<rundown_pos>';
	echo $row['rundown_pos'];
	echo '</rundown_pos>';
	
	echo '</segment>';
}
echo '</rundown>';

CloseCon($conn);
?>
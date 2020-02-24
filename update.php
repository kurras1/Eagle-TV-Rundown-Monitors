<?php
//ETV Rundown Applet - update.php
//Programmer - Michael Kurras

//Utility functions regarding the update time of the rundown

function setUpdatedTime(){
	global $conn;
	$now = time();
	$query = "INSERT INTO `datastore` (`key`, `value`) VALUES ('updated', '".$now."') ON DUPLICATE KEY UPDATE `value` = '".$now."';";
	$conn->query($query);
}
function getUpdatedTime(){
	global $conn;
	$query = "SELECT 'value' FROM `datastore` WHERE `key` = 'updated'";
	$result = $conn->query($query);
	return $result;
}

?>
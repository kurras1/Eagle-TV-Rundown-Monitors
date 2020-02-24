<?php
//ETV Rundown Applet - config.php
//Programmer - Michael Kurras

//Database functions located in here.

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//This Function opens the database connection and checks for proper configuration
function OpenCon()
{
//Establish Connection to Database
 $dbhost = "localhost";
 $dbuser = "etvRundown";
 $dbpass = "test123";
 $db = "etvRundown";
 $conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connect failed: %s\n". $conn -> error);

//Check for Rundown Table
//If Rundown Table Does Not Exist - Create it.
$sql = "CREATE TABLE IF NOT EXISTS `rundown` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`uid` VARCHAR(256) NOT NULL DEFAULT '0',
	`title` VARCHAR(256) NOT NULL DEFAULT '0',
	`producer` VARCHAR(256) NULL DEFAULT '0',
	`runtime` INT(11) NOT NULL DEFAULT 0,
	`audio` INT(11) NOT NULL DEFAULT 0,
	`type` INT(11) NULL DEFAULT NULL,
	`description` TEXT NOT NULL DEFAULT '0',
	`time_created` TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`rundown_pos` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `id` (`id`)
)";
$conn->query($sql);

//Check for Datastore Table
//If Datastore Table Does Not Exist - Create it.

$sql = "CREATE TABLE IF NOT EXISTS `datastore` (
`key` VARCHAR(256) NOT NULL PRIMARY KEY,
`value` BLOB)";
$conn->query($sql);

return $conn;
}
 
//This function closes the database connection
function CloseCon($conn)
 {
 $conn -> close();
 }
 
$conn = OpenCon();
   
?>
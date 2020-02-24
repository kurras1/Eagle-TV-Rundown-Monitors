<?php
//ETV Rundown Applet - add-segment.php
//Programmer - Michael Kurras

//Code to add a segment to the rundown

include 'config.php';
include 'update.php';

$error = array();
$data = array();

$segmentType = 0;
$segmentDesc = "";

// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array

if (empty($_POST['inputSegmentTitle']))
        $errors['inputSegmentTitle'] = 'A segment title is required';

if (empty($_POST['inputSegmentType'])){
        $errors['inputSegmentType'] = 'A type is required';
} else {
	switch ($_POST['inputSegmentType']) {
		case "Segment":
			$segmentType = 0;
			break;
		case "B-Roll":
			$segmentType = 1;
			break;
		case "Intro":
			$segmentType = 2;
			break;
		case "Live Studio Set":
			$segmentType = 3;
			break;		
		case "Live Greenscreen":
			$segmentType = 4;
			break;	
		case "Graphic / DVE":
			$segmentType = 5;
			break;
	}
}

if ((empty($_POST['inputSegmentProducer'])) && ($segmentType == 0 || $segmentType == 1))
        $errors['inputSegmentProducer'] = 'A name is required';	
	
if (($_POST['inputSegmentTRT'] == 0) && ($segmentType == 0 || $segmentType == 1 || $segmentType == 2))
        $errors['inputSegmentTRT'] = 'A TRT is required';
	
if ((empty($_POST['inputSegmentAudioConfig'])) && ($segmentType == 0 || $segmentType == 1 || $segmentType == 2))
        $errors['inputSegmentAudioConfig'] = 'An audio configuration is required';
	
if (empty($_POST['inputSegmentPos']))
        $errors['inputSegmentPos'] = 'A position is required';

if (empty ($_POST['inputSegmentDesc'])){
		$segmentDesc = "";
	} else {
		$segmentDesc = $_POST['inputSegmentDesc'];
	}
	
// return a response ===========================================================

    // if there are any errors in our errors array, return a success boolean of false
    if ( ! empty($errors)) {

        // if there are items in our errors array, return those errors
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {

        // if there are no errors process our form, then return a message

	$segmentName = $_POST['inputSegmentTitle'];
	$segmentProducer = $_POST['inputSegmentProducer'];
	$segmentTRT = $_POST['inputSegmentTRT'];
	if($segmentType == 3 || $segmentType == 4 || $segmentType == 5){
		$segmentTRT = -1;
	}
	$segmentAudioCfg = 0;
	$segmentPos = $_POST['inputSegmentPos'];
	$uid = dechex(time());

	switch ($_POST['inputSegmentAudioConfig']) {
		case "SOT":
			$segmentAudioCfg = 0;
			break;
		case "NAT":
			$segmentAudioCfg = 1;
			break;
		case "NAT+MUSIC":
			$segmentAudioCfg = 2;
			break;
		case "SILENT":
			$segmentAudioCfg = 3;
			break;
	}

	

	$sql = "INSERT INTO `etvrundown`.`rundown` (".
		"`uid`, `title`, `producer`, `runtime`, `audio`, `type`, `description`, `rundown_pos`) ".
		"VALUES ('".$uid."', '".$segmentName."', '".$segmentProducer."', '".$segmentTRT."', '".$segmentAudioCfg."', '".$segmentType."', '".$segmentDesc."', '".$segmentPos."');";
	$conn->query($sql);

	CloseCon($conn);

    // show a message of success and provide a true success variable
    $data['success'] = true;
    $data['message'] = 'Segment Added!';
    }

    // return all our data to an AJAX call
    echo json_encode($data);

?>
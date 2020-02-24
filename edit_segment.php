<?php
//ETV Rundown Applet - edit-segment.php
//Programmer - Michael Kurras

//Code to edit the a segment of the rundown

include 'config.php';
include 'update.php';

$error = array();
$data = array();

$segmentType = 0;
$segmentDesc = "";

// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array


if (empty($_POST['editSegmentTitle']))
        $errors['editSegmentTitle'] = 'A segment title is required';
	
if (empty($_POST['editSegmentType'])) {
        $errors['editSegmentType'] = 'A type is required';
} else {
	switch ($_POST['editSegmentType']) {
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

if ((empty($_POST['editSegmentProducer'])) && ($segmentType == 0 || $segmentType == 1))
        $errors['editSegmentProducer'] = 'A name is required';	
	
if (($_POST['editSegmentTRT'] == 0) && ($segmentType == 0 || $segmentType == 1 || $segmentType == 2))
        $errors['editSegmentTRT'] = 'A TRT is required';
	
if ((empty($_POST['editSegmentAudioConfig'])) && ($segmentType == 0 || $segmentType == 1 || $segmentType == 2))
        $errors['editSegmentAudioConfig'] = 'An audio configuration is required';

if (empty ($_POST['editSegmentDesc'])){
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

	$segmentName = $_POST['editSegmentTitle'];
	$segmentProducer = $_POST['editSegmentProducer'];
	$segmentTRT = $_POST['editSegmentTRT'];
	if($segmentType == 3 || $segmentType == 4 || $segmentType == 5){
		$segmentTRT = -1;
	}
	$segmentAudioCfg = 0;
	$segmentDesc = $_POST['editSegmentDesc'];
	$uid = $_POST['segmentUID'];

	switch ($_POST['editSegmentAudioConfig']) {
		case "SOT":
			$segmentAudioCfg = 0;
			break;
		case "NAT":
			$segmentAudioCfg = 1;
			break;
		case "NAT+MUSIC":
			$segmentAudioCfg = 2;
			break;
		case "NAT+VOX":
			$segmentAudioCfg = 3;
			break;
		case "SILENT":
			$segmentAudioCfg = 4;
			break;
	}

	$sql = "UPDATE `etvrundown`.`rundown` SET ".
		"`title`=\"".$segmentName.
		"\", `producer`=\"".$segmentProducer.
		"\", `runtime`=".$segmentTRT.
		", `audio`=".$segmentAudioCfg.
		", `type`=".$segmentType.
		", `description`=\"".$segmentDesc.
		"\" WHERE `uid`='".$uid."';";
	$conn->query($sql);
	setUpdatedTime();
	CloseCon($conn);


    // show a message of success and provide a true success variable
    $data['success'] = true;
    $data['message'] = 'Segment Edited!';
    }

    // return all our data to an AJAX call
    echo json_encode($data);

?>
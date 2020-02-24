<?php
//ETV Rundown Applet - set-order.php
//Programmer - Michael Kurras

//code to set the order of the rundown
include 'config.php';
include 'update.php';

$error = array();
$data = array();

// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array

if (empty($_POST['data']))
	$errors['data'] = 'No Data';
	
	
	
// return a response ===========================================================

    // if there are any errors in our errors array, return a success boolean of false
    if ( ! empty($errors)) {

        // if there are items in our errors array, return those errors
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {
		
	$segmentOrder = $_POST['data'];
	$sqlDynamicQuery1 = "";
	$sqlDynamicQuery2 = "";
	$someObject = json_decode($_POST['data'], true);
	$comma = false;
	foreach($someObject as $key => $value) {
		$sqlDynamicQuery1 .= "WHEN uid = '".$key."' THEN ".$value." ";
		if($comma){
			$sqlDynamicQuery2 .= ',';
		}
		$sqlDynamicQuery2 .= "'".$key."'";
		$comma = true;
		
	}
	$sqlBaseQuery = "UPDATE `etvrundown`.`rundown` SET `rundown_pos` = CASE ";
	$sqlBaseQuery2 = "END WHERE uid IN (";
	$sqlBaseQuery3 = ");";

    // if there are no errors process our form, then return a message
	//$sql = "UPDATE `etvrundown`.`rundown` SET `rundown_pos` = CASE `uid` WHEN *uid* THEN *value* END;";
	//USE THIS QUERY :: UPDATE `etvrundown`.`rundown` SET `rundown_pos` = CASE WHEN uid = '5e44586d' THEN 157 WHEN uid = '5e445871' THEN 156 END WHERE uid IN ('5e44586d','5e445871');
	
	
	$sql = "{$sqlBaseQuery}{$sqlDynamicQuery1}{$sqlBaseQuery2}{$sqlDynamicQuery2}{$sqlBaseQuery3}";
	$conn->query($sql);
	setUpdatedTime();
	CloseCon($conn);

    // show a message of success and provide a true success variable
    $data['success'] = true;
    $data['message'] = 'Order Updated On Server!';
    }

    // return all our data to an AJAX call
    echo json_encode($data);
?>
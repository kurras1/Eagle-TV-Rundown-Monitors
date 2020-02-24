<?php
include 'config.php';
include 'update.php';

$error = array();
$data = array();

// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array

if (empty($_POST['segmentFocus'])){
	$errors['segmentFocus'] = 'A segment focus is required';
} else {
	
	$query = "SELECT 1 FROM rundown WHERE `uid` = '".$_POST['segmentFocus']."'";
	$result = $conn->query($query);
	
	if(mysqli_num_rows($result)==0)
		$errors['error'] = 'Segment Not Found at '.$_POST['segmentFocus'];
	
}
	
	
	
// return a response ===========================================================

    // if there are any errors in our errors array, return a success boolean of false
    if ( ! empty($errors)) {

        // if there are items in our errors array, return those errors
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {

        // if there are no errors process our form, then return a message

	//$segmentName = $_POST['inputSegmentTitle'];

	//$conn = OpenCon();

	$sql = "DELETE FROM `rundown` WHERE `uid`='".$_POST['segmentFocus']."';";
	$conn->query($sql);

	CloseCon($conn);

    // show a message of success and provide a true success variable
    $data['success'] = true;
    $data['message'] = 'Segment Deleted!';
    }

    // return all our data to an AJAX call
    echo json_encode($data);
?>
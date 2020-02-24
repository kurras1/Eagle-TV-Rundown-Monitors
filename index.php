<?php
//ETV Rundown Applet - index.php
//Programmer - Michael Kurras

//Main page and forms for everything
include 'config.php';

$conn = OpenCon();

CloseCon($conn);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Eagle TV 2.0 Rundown</title>

    <!-- Bootstrap core CSS -->
	<link href="css/jquery-ui.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
	
	<link href="css/all.css" rel="stylesheet">
	<link href="css/etv.css" rel="stylesheet">

    <!-- Custom styles for this template -->
	
  </head>

  <body onload="updateRundown()">

    <nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#">
	  Eagle TV 2.0 Rundown Controller</a>
    </nav>

    <div class="container mt-2">
	<div class="row">
	<div class="col-md-12">
		<div class="card bg-light mb-3">
			<h5 class="card-header">Show Rundown</h5>
			<div id="sortable">
			</div>
			<div id="addSegmentButton" class="card-body ui-state-default" style="cursor: pointer;">
				<center><i class="fas fa-plus-circle"></i></center>
			</div>

			<div class="card-footer">
				<small class="text-muted" id="lastUpdatedTime">Updating...</small>
			</div>
		</div>
	</div>
	</div>
    </div><!-- /.container -->
	
	  <!-- Form to add segment
    ================================================== -->
	
	<div class="modal fade" id="addSegmentModal" tabindex="-1" role="dialog" aria-labelledby="addSegmentModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Add Segment</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="addSegmentForm">
						<div class="form-group" id="segment-title-group">
							<label for="inputSegmentTitle">Segment Title</label>
							<input type="text" class="form-control" id="inputSegmentTitle" name="inputSegmentTitle" placeholder="Segment Title" required>
							<small hidden class="invalid-feedback">A Title is Required</small>
						</div>
						<div class="form-group" id="segment-type-group">
							<label for="inputSegmentType">Type</label>
							<select class="form-control" id="inputSegmentType" name="inputSegmentType" placeholder="SELECT">
								<option value="" disabled selected>Select</option>
								<option>Segment</option>
								<option>B-Roll</option>
								<option>Intro</option>
								<option>Live Studio Set</option>
								<option>Live Greenscreen</option>
								<option>Graphic / DVE</option>
							</select>
							<small hidden class="invalid-feedback">A Type is Required</small>
						</div>
						<div class="form-group" id="segment-producer-group">
							<label for="inputSegmentProducer">Segment Producer</label>
							<input type="text" class="form-control" id="inputSegmentProducer" name="inputSegmentProducer" placeholder="Firstname Lastname" required>
							<small hidden class="invalid-feedback">A Name is Required</small>
						</div>
						<div class="form-group" id="segment-runtime-group">
							<label for="inputSegmentTRT">Total Runtime</label>
							<input type="text" class="form-control input-time" id="inputSegmentTRT" name="inputSegmentTRT" placeholder="MM:SS" required>
							<small hidden class="invalid-feedback">A Total Runtime is Required</small>
						</div>
							
						<div class="form-group" id="segment-audio-group">
							<label for="inputSegmentAudioConfig">Audio Configuration</label>
							<select class="form-control" id="inputSegmentAudioConfig" name="inputSegmentAudioConfig" placeholder="SELECT">
								<option value="" disabled selected>Select</option>
								<option>SOT</option>
								<option>NAT</option>
								<option>NAT+MUSIC</option>
								<option>SILENT</option>
							</select>
							<small hidden class="invalid-feedback">An Audio Configuration is Required</small>
						</div>
						<div class="form-group">
							<label for="inputSegmentDesc">Description / Instructions</label>
							<input type="text" class="form-control" id="inputSegmentDesc" name="inputSegmentDesc" placeholder="Description" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="submitAddSegmentForm">Add</button>
				</div>
			</div>
		</div>
	</div>
	
	  <!-- Form to Edit Segment
    ================================================== -->
	
	<div class="modal fade" id="editSegmentModal" tabindex="-1" role="dialog" aria-labelledby="editSegmentModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Edit Segment</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="editSegmentForm">
						<div class="form-group">
							<label for="editSegmentTitle">Segment Title</label>
							<input type="text" class="form-control" id="editSegmentTitle" name="editSegmentTitle" placeholder="Segment Title" required>
							<small hidden class="invalid-feedback">A Title is Required</small>
						</div>
						<div class="form-group" id="edit-segment-type-group">
							<label for="editSegmentType">Type</label>
							<select class="form-control" id="editSegmentType" name="editSegmentType" placeholder="SELECT">
								<option value="" disabled selected>Select</option>
								<option>Segment</option>
								<option>B-Roll</option>
								<option>Intro</option>
								<option>Live Studio Set</option>
								<option>Live Greenscreen</option>
								<option>Graphic / DVE</option>
							</select>
							<small hidden class="invalid-feedback">A Type is Required</small>
						</div>
						<div class="form-group" id="edit-segment-producer-group">
							<label for="editSegmentProducer">Segment Producer</label>
							<input type="text" class="form-control" id="editSegmentProducer" name="editSegmentProducer" placeholder="Firstname Lastname" required>
							<small hidden class="invalid-feedback">A Name is Required</small>
						</div>
						<div class="form-group" id="edit-segment-runtime-group">
							<label for="editSegmentTRT">Total Runtime</label>
							<input type="text" class="form-control edit-time" id="editSegmentTRT" name="editSegmentTRT" placeholder="MM:SS" required>
							<small hidden class="invalid-feedback">A TRT is Required</small>
						</div>
						<div class="form-group" id="edit-segment-audio-group">
							<label for="editSegmentAudioConfig">Audio Configuration</label>
							<select class="form-control" id="editSegmentAudioConfig" name="editSegmentAudioConfig" placeholder="SELECT">
								<option value="" disabled selected>Select</option>
								<option>SOT</option>
								<option>NAT</option>
								<option>NAT+MUSIC</option>
								<option>SILENT</option>
							</select>
							<small hidden class="invalid-feedback">A Audio Configuration is Required</small>
						</div>
						<div class="form-group">
							<label for="editSegmentDesc">Description / Instructions</label>
							<input type="text" class="form-control" id="editSegmentDesc" name="editSegmentDesc" placeholder="Description" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="submitEditSegmentForm">Save</button>
				</div>
			</div>
		</div>
	</div>
	
	<footer class="footer mt-auto py-3">
		<div class="container">
			<span class="text-muted">Designed by Michael Kurras<br>Icons by Font-Awesome</span>
		</div>
	</footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/cleave.min.js"></script>
	<script src="js/form-process.js"></script>
	
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

  </body>
</html>

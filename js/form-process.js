//ETV Rundown Applet - form-process.php
//Programmer - Michael Kurras

//Code regarding the submission, reordering, and editing of segments
$(document).ready(function() {
	
	var cleave = new Cleave('.input-time', {
		time: true,
		timePattern: ['m', 's']
	});
	
	var editCleave = new Cleave('.edit-time', {
		time: true,
		timePattern: ['m', 's']
	});
	
	$( function() {
		$( "#sortable" ).sortable();
		$( "#sortable" ).disableSelection();
		$( "#date" ).datepicker();
	} );
	$("#addSegmentButton").click(function() {
		$("#segment-title-group .invalid-feedback").attr('hidden',true);
		$("#segment-type-group .invalid-feedback").attr('hidden',true);
		$("#segment-producer-group .invalid-feedback").attr('hidden',true);
		$("#segment-runtime-group .invalid-feedback").attr('hidden',true);
		$("#segment-audio-group .invalid-feedback").attr('hidden',true);
		
		document.getElementById("inputSegmentTitle").value = "";
		document.getElementById("inputSegmentType").value = "";
		document.getElementById("inputSegmentProducer").value = "";
		document.getElementById("inputSegmentTRT").value = "";
		document.getElementById("inputSegmentAudioConfig").value = "";
		document.getElementById("inputSegmentDesc").value = "";
		$("#addSegmentModal").modal();
	});
	$("#submitAddSegmentForm").click(function() {
		$("#addSegmentForm").submit();
	});
	
	$("#submitEditSegmentForm").click(function() {
		$("#editSegmentForm").submit();
	});
		
	//update rundown
	setInterval(function(){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200 && !pauseAjax) {
				printRundown(this);
			}
		};
		xhttp.open("GET", "request.php", true);
		xhttp.send();
	},2000)

    // process the form
    $('#addSegmentForm').submit(function(event) {
		
        // get the form data
        // there are many ways to get this data using jQuery (you can use the class or id also)
		var timeExploded = $('#inputSegmentTRT').val().split(":");
		var trtInSec = (parseInt(timeExploded[0])*60)+parseInt(timeExploded[1]);
		if(isNaN(trtInSec)){
			trtInSec = 0;
		}
		if($('#inputSegmentType').val() == "Live Studio Set" || 
			$('#inputSegmentType').val() == "Live Greenscreen" || 
			$('#inputSegmentType').val() == "Graphics / DVE") {
			trtInSec = -1;
		}
        var formData = {
            'inputSegmentTitle'         : HTMLEncode($('#inputSegmentTitle').val()),
            'inputSegmentProducer'      : $('#inputSegmentProducer').val(),
            'inputSegmentType'   		: $('#inputSegmentType').val(),
			'inputSegmentTRT'    		: trtInSec,
			'inputSegmentAudioConfig'   : $('#inputSegmentAudioConfig').val(),
			'inputSegmentDesc'    		: HTMLEncode($('#inputSegmentDesc').val()),
			'inputSegmentPos'			: getNextRundown()
        };
        // process the form
        $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : 'add_segment.php', // the url where we want to POST
            data        : formData, // our data object
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true
        })
            // using the done promise callback
            .done(function(data) {

                // log data to the console so we can see
                console.log(data); 
				
				if(data.hasOwnProperty("success")){
					if(data.success == true){
						$("#addSegmentModal").modal('hide');
						$("#segment-title-group .invalid-feedback").attr('hidden',true);
						$("#segment-type-group .invalid-feedback").attr('hidden',true);
						$("#segment-producer-group .invalid-feedback").attr('hidden',true);
						$("#segment-runtime-group .invalid-feedback").attr('hidden',true);
						$("#segment-audio-group .invalid-feedback").attr('hidden',true);
						updateRundown();
					} else if (data.hasOwnProperty("errors")){
						console.log("Error with input");
						if(data.errors.hasOwnProperty("inputSegmentTitle")){
							$("#segment-title-group .invalid-feedback").removeAttr('hidden');
						} else {
							$("#segment-title-group .invalid-feedback").attr('hidden',true);
						}
						if(data.errors.hasOwnProperty("inputSegmentType")){
							$("#segment-type-group .invalid-feedback").removeAttr('hidden');
						} else {
							$("#segment-type-group .invalid-feedback").attr('hidden',true);
						}
						if(data.errors.hasOwnProperty("inputSegmentProducer")){
							$("#segment-producer-group .invalid-feedback").removeAttr('hidden');
						} else {
							$("#segment-producer-group .invalid-feedback").attr('hidden',true);
						}
						if(data.errors.hasOwnProperty("inputSegmentTRT")){
							$("#segment-runtime-group .invalid-feedback").removeAttr('hidden');
						} else {
							$("#segment-runtime-group .invalid-feedback").attr('hidden',true);
						}
						if(data.errors.hasOwnProperty("inputSegmentAudioConfig")){
							$("#segment-audio-group .invalid-feedback").removeAttr('hidden');
						} else {
							$("#segment-audio-group .invalid-feedback").attr('hidden',true);
						}
					}
				}

                // here we will handle errors and validation messages
				
            });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
    });
	
	
	
	$('#editSegmentForm').submit(function(event) {
		
        // get the form data
        // there are many ways to get this data using jQuery (you can use the class or id also)
		var timeExploded = $('#editSegmentTRT').val().split(":");
		var trtInSec = (parseInt(timeExploded[0])*60)+parseInt(timeExploded[1]);
		if(isNaN(trtInSec)){
			trtInSec = 0;
		}
		if($('#inputSegmentType').val() == "Live Studio Set" || 
			$('#inputSegmentType').val() == "Live Greenscreen" || 
			$('#inputSegmentType').val() == "Graphics / DVE") {
			trtInSec = -1;
		}
        var formData = {
            'editSegmentTitle'          : HTMLEncode($('#editSegmentTitle').val()),
            'editSegmentProducer'       : $('#editSegmentProducer').val(),
            'editSegmentType'   		: $('#editSegmentType').val(),
			'editSegmentTRT'    		: trtInSec,
			'editSegmentAudioConfig'    : $('#editSegmentAudioConfig').val(),
			'editSegmentDesc'    		: HTMLEncode($('#editSegmentDesc').val()),
			'segmentUID'				: currentlyEditing
        };
        // process the form
        $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : 'edit_segment.php', // the url where we want to POST
            data        : formData, // our data object
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true
        })
            // using the done promise callback
            .done(function(data) {

                // log data to the console so we can see
                console.log(data); 
				
				if(data.hasOwnProperty("success")){
					if(data.success == true){
						$("#editSegmentModal").modal('hide');
						$("#edit-segment-title-group .invalid-feedback").attr('hidden',true);
						$("#edit-segment-type-group .invalid-feedback").attr('hidden',true);
						$("#edit-segment-producer-group .invalid-feedback").attr('hidden',true);
						$("#edit-segment-runtime-group .invalid-feedback").attr('hidden',true);
						$("#edit-segment-audio-group .invalid-feedback").attr('hidden',true);
						updateRundown();
					} else if (data.hasOwnProperty("errors")){
					console.log("Error with input");
					if(data.errors.hasOwnProperty("editSegmentTitle")){
						$("#edit-segment-title-group .invalid-feedback").removeAttr('hidden');
					} else {
						$("#edit-segment-title-group .invalid-feedback").attr('hidden',true);
					}
					if(data.errors.hasOwnProperty("editSegmentType")){
						$("#edit-segment-type-group .invalid-feedback").removeAttr('hidden');
					} else {
						$("#edit-segment-type-group .invalid-feedback").attr('hidden',true);
					}
					if(data.errors.hasOwnProperty("editSegmentProducer")){
						$("#edit-segment-producer-group .invalid-feedback").removeAttr('hidden');
					} else {
						$("#edit-segment-producer-group .invalid-feedback").attr('hidden',true);
					}
					if(data.errors.hasOwnProperty("editSegmentTRT")){
						$("#edit-segment-runtime-group .invalid-feedback").removeAttr('hidden');
					} else {
						$("#edit-segment-runtime-group .invalid-feedback").attr('hidden',true);
					}
					if(data.errors.hasOwnProperty("editSegmentAudioConfig")){
						$("#edit-segment-audio-group .invalid-feedback").removeAttr('hidden');
					} else {
						$("#edit-segment-audio-group .invalid-feedback").attr('hidden',true);
					}
					}
				}
            });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
    });
	
	$('#inputSegmentType').on('input', function(e) {
		switch( $(this).val() ){
			case 'Intro':
				$('#segment-producer-group').hide();
				$('#segment-runtime-group').show();
				$('#segment-audio-group').show();
			break;
			case 'Live Studio Set':
				$('#segment-producer-group').hide();
				$('#segment-runtime-group').hide();
				$('#segment-audio-group').hide();
			break;
			case 'Live Greenscreen':
				$('#segment-producer-group').hide();
				$('#segment-runtime-group').hide();
				$('#segment-audio-group').hide();
			break;
			case 'Graphic / DVE':
				$('#segment-producer-group').hide();
				$('#segment-runtime-group').hide();
				$('#segment-audio-group').hide();
			break;
			default:
				$('#segment-producer-group').show();
				$('#segment-runtime-group').show();
				$('#segment-audio-group').show();
		}
	});
	
	$('#editSegmentType').on('input', function(e) {
		switch( $(this).val() ){
			case 'Intro':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').show();
				$('#edit-segment-audio-group').show();
			break;
			case 'Live Studio Set':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').hide();
				$('#edit-segment-audio-group').hide();
			break;
			case 'Live Greenscreen':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').hide();
				$('#edit-segment-audio-group').hide();
			break;
			case 'Graphic / DVE':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').hide();
				$('#edit-segment-audio-group').hide();
			break;
			default:
				$('#edit-segment-producer-group').show();
				$('#edit-segment-runtime-group').show();
				$('#edit-segment-audio-group').show();
		}
	});

});
var rundownItems = 0;
function getNextRundown(){
	return rundownItems+1;
}
function setRundownItems(x){
	rundownItems = x;
}

// Delete Segment Function
	function deleteSegmentByUID(uid){
		var formData = {
            'segmentFocus'         : uid
        };
		$.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : 'delete_segment.php', // the url where we want to POST
            data        : formData, // our data object
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true
        })
            // using the done promise callback
            .done(function(data) {

                // log data to the console so we can see
                console.log(data); 
				if(data.hasOwnProperty("success")){
					if(data.success == true){
						updateRundown();
					}
				}
                // here we will handle errors and validation messages
            });
	}
//Update Rundown
	
	function printRundown(xml) {
		console.log("Updating Rundown");
		var i;
		var items = '';
		var xmlDoc = xml.responseXML;
		var totalRundown = 0;
		var lastUpdatedTime = xmlDoc.getElementsByTagName("updated")[0].childNodes[0].nodeValue;
		var x = xmlDoc.getElementsByTagName("segment");
		for (i = 0; i <x.length; i++) { 
			var uid = x[i].getElementsByTagName("uid")[0].childNodes[0].nodeValue;
			var rPos = x[i].getElementsByTagName("rundown_pos")[0].childNodes[0].nodeValue;
			var segmentType = x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue;
			var badgeType = "info";
			if (segmentType == "Intro"){
				badgeType = "primary";
			} else if (segmentType == "Greenscreen"){
				badgeType = "success";
			} else if (segmentType == "B-Roll"){
				badgeType = "warning";
			} else if (segmentType == "Graphic / DVE"){
				badgeType = "danger";
			}
			items += '<div class="card-body ui-state-default" onmousedown="setPause(true)" onmouseup="updateServerOrder()" id="' +
				uid +
				'"><h5 class="card-title">' +
				x[i].getElementsByTagName("title")[0].childNodes[0].nodeValue +
				' <span class="badge badge-'+ badgeType +'"> ' +
				x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue +
				'</span>' +
				'<div class="segment-control-block"><a onclick="openEditSegmentForm(\'' +
				uid +
				'\'\)" href="javascript:void(0);" class="segment-control"><i class="fas fa-pen"></i></a>  <a onclick="deleteSegmentByUID(\'' +
				uid +
				'\'\)" href="javascript:void(0);" class="segment-control"><i class="fas fa-trash-alt"></i></a>' +
				'</div></h5><h6 class="card-subtitle mb-2 text-muted">';
				if(x[i].getElementsByTagName("trt")[0].childNodes[0].nodeValue != "LIVE"){
					items += 'TRT ';
				}
				items += x[i].getElementsByTagName("trt")[0].childNodes[0].nodeValue;
			if(x[i].getElementsByTagName("trt")[0].childNodes[0].nodeValue != "LIVE"){
				items += ' | ' + x[i].getElementsByTagName("audio")[0].childNodes[0].nodeValue;
			}
			if(x[i].getElementsByTagName("producer")[0].childNodes[0] == undefined || 
				x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue == "Intro" || 
				x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue == "Live Studio Set" || 
				x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue == "Live Greenscreen" || 
				x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue == "Graphic / DVE" ){
			} else {
				items += ' | ' + x[i].getElementsByTagName("producer")[0].childNodes[0].nodeValue;
			}
			if(x[i].getElementsByTagName("description")[0].childNodes[0] != undefined){
				items += ' | ' + x[i].getElementsByTagName("description")[0].childNodes[0].nodeValue;
			}
			items += '</h6></div>';
			}
			if(rPos > totalRundown){
				totalRundown = rPos;
			}
		document.getElementById("sortable").innerHTML = items;
		document.getElementById("lastUpdatedTime").innerHTML = timeDifference(lastUpdatedTime);
		setRundownItems(totalRundown);
	}
	function updateRundown(){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				printRundown(this);
			}
		};
		xhttp.open("GET", "request.php", true);
		xhttp.send();
	}
	
//Detech ReArrange In Progress
	var pauseAjax = false;
	function setPause(x){
		if(x == true){
			console.log("Updates Paused");
		} else {
			console.log("Updates Resumed");
		}
		pauseAjax = x;
	}
//Update Server with new order
	var previousOrder = "";
	function updateServerOrder(){
		setTimeout(function (){
			var obj = {};
			var i = 0;
			$('#sortable > div').map(function() {
				if(this.id != ""){
					i++;
					obj[this.id] = i;
				}
			});
			var ajaxData = {
				'data'		: JSON.stringify(obj)
			};
			if(ajaxData.data == previousOrder){
				console.log("No Change in Order Detected!");
				setPause(false);
			} else {
				$.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         : 'set_order.php', // the url where we want to POST
				data        : ajaxData, // our data object
				dataType    : 'json', // what type of data do we expect back from the server
				encode      : true
			})
            // using the done promise callback
            .done(function(data) {

                // log data to the console so we can see
                console.log(data); 
				if(data.hasOwnProperty("success")){
					if(data.success == true){
						updateRundown();
						previousOrder = ajaxData.data;
						setPause(false);
					}
				}
                // here we will handle errors and validation messages
            });
			}
			
		}, 100);
		
	}
//Time Math
function timeDifference(previous) {
	var current = new Date().getTime()
	current = Math.round(current/1000);
    var msPerMinute = 60;
    var msPerHour = msPerMinute * 60;
    var msPerDay = msPerHour * 24;
    var msPerMonth = msPerDay * 30;
    var msPerYear = msPerDay * 365;

    var elapsed = current - previous;

    if (elapsed < msPerMinute) {
         return 'Updated less than a minute ago';   
    }

    else if (elapsed < msPerHour) {
         return 'Updated ' + Math.round(elapsed/msPerMinute) + ' minutes ago';   
    }

    else if (elapsed < msPerDay ) {
         return 'Updated ' + Math.round(elapsed/msPerHour ) + ' hours ago';   
    }

    else if (elapsed < msPerMonth) {
        return 'Updated approximately ' + Math.round(elapsed/msPerDay) + ' days ago';   
    }

    else if (elapsed < msPerYear) {
        return 'Updated approximately ' + Math.round(elapsed/msPerMonth) + ' months ago';   
    }

    else {
        return 'Updated approximately ' + Math.round(elapsed/msPerYear ) + ' years ago';   
    }
}
// Open Edit Form
var currentlyEditing = "";
function openEditSegmentForm(editUID){
	$("#edit-segment-title-group .invalid-feedback").attr('hidden',true);
	$("#edit-segment-type-group .invalid-feedback").attr('hidden',true);
	$("#edit-segment-producer-group .invalid-feedback").attr('hidden',true);
	$("#edit-segment-runtime-group .invalid-feedback").attr('hidden',true);
	$("#edit-segment-audio-group .invalid-feedback").attr('hidden',true);
	$("#editSegmentModal").modal();
	currentlyEditing = editUID;
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			populateEditForm(this, editUID);
		}
	};
	xhttp.open("GET", "request.php", true);
	xhttp.send();
}
	function populateEditForm(xml, editUID) {
		console.log("Placing values in edit form");
		var i;
		var xmlDoc = xml.responseXML;
		var x = xmlDoc.getElementsByTagName("segment");
		for (i = 0; i <x.length; i++) { 
			var uid = x[i].getElementsByTagName("uid")[0].childNodes[0].nodeValue;
			if(uid == editUID){
				document.getElementById("editSegmentTitle").value = 
					x[i].getElementsByTagName("title")[0].childNodes[0].nodeValue;
				if(x[i].getElementsByTagName("producer")[0].childNodes[0] != undefined){
					document.getElementById("editSegmentProducer").value =
						x[i].getElementsByTagName("producer")[0].childNodes[0].nodeValue;
				}
				var typeOfSegment = "";
				switch(x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue){
					case 'Greenscreen':
						typeOfSegment = 'Live Greenscreen';
					break;
					case 'Studio Set':
						typeOfSegment = 'Live Studio Set';
					break
					default:
						typeOfSegment = x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue;
					
				}
				editFormFieldHide(typeOfSegment);
				document.getElementById("editSegmentType").value =	typeOfSegment;
					editFormFieldHide(typeOfSegment);
				if(x[i].getElementsByTagName("trt")[0].childNodes[0] != undefined){
					if(x[i].getElementsByTagName("trt")[0].childNodes[0].nodeValue == "LIVE"){
						console.log("It Says Live");
						document.getElementById("editSegmentTRT").value = "";
					} else {
						document.getElementById("editSegmentTRT").value =
							x[i].getElementsByTagName("trt")[0].childNodes[0].nodeValue;
					}
				} 
				if(x[i].getElementsByTagName("audio")[0].childNodes[0] != undefined){
					document.getElementById("editSegmentAudioConfig").value =
						x[i].getElementsByTagName("audio")[0].childNodes[0].nodeValue;
				}
				if(x[i].getElementsByTagName("description")[0].childNodes[0] != undefined){
					document.getElementById("editSegmentDesc").value =
						x[i].getElementsByTagName("description")[0].childNodes[0].nodeValue;
				}
			}
		}
	}
	function editFormFieldHide(segmentTypeSelected){
		switch( segmentTypeSelected ){
			case 'Intro':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').show();
				$('#edit-segment-audio-group').show();
			break;
			case 'Live Studio Set':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').hide();
				$('#edit-segment-audio-group').hide();
			break;
			case 'Live Greenscreen':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').hide();
				$('#edit-segment-audio-group').hide();
			break;
			case 'Graphic / DVE':
				$('#edit-segment-producer-group').hide();
				$('#edit-segment-runtime-group').hide();
				$('#edit-segment-audio-group').hide();
			break;
			default:
				$('#edit-segment-producer-group').show();
				$('#edit-segment-runtime-group').show();
				$('#edit-segment-audio-group').show();
		}
	}
	function HTMLEncode(str) {
    var i = str.length,
        aRet = [];

    while (i--) {
        var iC = str[i].charCodeAt();
        if (iC < 65 || iC > 127 || (iC>90 && iC<97)) {
            aRet[i] = '&#'+iC+';';
        } else {
            aRet[i] = str[i];
        }
    }
		return aRet.join('');
	}
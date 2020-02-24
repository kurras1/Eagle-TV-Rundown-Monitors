//ETV Rundown Applet - rundown-process.js
//Programmer - Michael Kurras

//Displays rundown information on monitor.php
$(document).ready(function() {
		
	//update rundown
	setInterval(function(){
		updateRundown();
	},2000)
});
//Update Rundown
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
	function printRundown(xml) {
		console.log("Updating Rundown");
		var i;
		var items = '';
		var xmlDoc = xml.responseXML;
		var x = xmlDoc.getElementsByTagName("segment");
		for (i = 0; i <x.length; i++) { 
			var colorSwatch = "default";
			var segmentType = x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue;
			if(segmentType == "Greenscreen"){
				colorSwatch = "green";
			} else if (segmentType == "Intro"){
				colorSwatch = "purple";
			} else if (segmentType == "B-Roll"){
				colorSwatch = "yellow";
			} else if (segmentType == "Graphic / DVE"){
				colorSwatch = "magenta";
			} else if (segmentType == "Segment"){
				colorSwatch = "blue";
			}
			items += '<tr><td class="left-col"><h1>'+
			x[i].getElementsByTagName("rundown_pos")[0].childNodes[0].nodeValue
			+'</h1></td><td class="rundown-'+ colorSwatch +' primary-col"><h2>'+
			x[i].getElementsByTagName("title")[0].childNodes[0].nodeValue +
			'</h2><h3>'+
			x[i].getElementsByTagName("type")[0].childNodes[0].nodeValue;
			if(x[i].getElementsByTagName("description")[0].childNodes[0] != undefined){
				items += ' | ' + x[i].getElementsByTagName("description")[0].childNodes[0].nodeValue;
			}
			items += '</h3></td><td class="rundown-'+ colorSwatch +' right-col">';
			var liveSize = 2;
			if(x[i].getElementsByTagName("trt")[0].childNodes[0].nodeValue == "LIVE"){
				liveSize = 1;
			}
			items += '<h' + 2 + '>' + x[i].getElementsByTagName("trt")[0].childNodes[0].nodeValue + '</h' + 2 + '>';
			if(liveSize == 2){
				items += '<h3>' + x[i].getElementsByTagName("audio")[0].childNodes[0].nodeValue + '</h3>';
			}
			items += '</td></tr>';
		}
		document.getElementById("rundown-table").innerHTML = items;
	}
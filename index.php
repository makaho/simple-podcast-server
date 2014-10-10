<?php 
require("config.php");
//handle actions
if (isset($_GET['action'])) {
	if (strcmp($_GET['action'],"delete") ==0) {
		//echo "action = delete";
		$db_delhandle  = new SQLite3($db_filename);
		$db_delhandle->exec("CREATE TABLE IF NOT EXISTS podcast_entries (title text, guid text, description text, link text, filename text, filetype text, filesize int, timeadded int, coverlink text, length int );");
		//get correspoonding filename
		$filenamestmt = $db_delhandle->prepare('SELECT * FROM podcast_entries WHERE guid=:guid');
		$filenamestmt->bindValue(':guid', $_GET['guid'], SQLITE3_TEXT);
		$filenameresult = $filenamestmt->execute();
		$res = $filenameresult->fetchArray(SQLITE3_ASSOC);
		
		$delfilename = $res["filename"];
		//delete file	
		unlink($delfilename);
		if (strcmp($res["coverlink"], $podcast_no_cover_image ) != 0) {
			unlink($res["coverlink"]);
		}
		//delete db entry
		$delstmt = $db_delhandle->prepare('DELETE FROM podcast_entries WHERE guid=:guid');
		$delstmt->bindValue(':guid', $_GET['guid'], SQLITE3_TEXT);
		$delstmt->execute();
		$db_delhandle->close();
	}
}

?>	

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="design/design.css" />
		<script>
				function hasClass(ele,cls) {
			    	return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
				}
			
				function removeClass(ele,cls) {
			        if (hasClass(ele,cls)) {
			            var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
			            ele.className=ele.className.replace(reg,' ');
			        }
			   	}
			   	function addClass(ele, cls) {
			        if (!hasClass(ele,cls)) {
				   		ele.className=ele.className + ' ' + cls;
				   	}
			   	}
    		</script>
	</head>
	<body>
	<a href="https://github.com/makaho/simple-podcast-server"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/365986a132ccd6a44c23a9169022c0b5c890c387/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f7265645f6161303030302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png"></a>	<div id="content">
	<div id="content">
	<h1>Simple Podcast Server</h1>
	<h2>Upload new episodes </h2>
	<div id="uploadbox">
  		<div id="holder">
  		</div> 
  		<br />
  		<br />
		<form name='file' method="post" enctype='multipart/form-data' id="uplaodform">
			<label for"file">Select a podcast file for upload:</label>
			<br />
			<br />
			<input type="file" name="file" id="file" />
		</form>
  		<progress id="uploadprogress" min="0" max="100" value="0">0</progress>
	</div>
	<div id="fallback" class="hidden">
			<form action="upload.php" name='file' method="post" enctype='multipart/form-data' id="uplaodform">
			<label for"file">Select a podcast file for upload:</label>
			<input type="file" name="file" />
			<input type="text" name="oldschool" value="oldschool" hidden="hidden" />
			<input type="submit" name="submit" value="Upload" />
		</form>
	</div>
	
<script>
	var holder = document.getElementById('holder'),
	    filereader = document.getElementById('filereader'),
	    formdata = document.getElementById('formdata'),
	    progress = document.getElementById('uploadprogress'),
	    fileupload = document.getElementById('upload');
	    uploadformbrowse = document.getElementById('file');

	if (( window.FormData === undefined ) ||
		( window.FileReader === undefined ) ||
		( window.FormData === undefined )) {
		removeClass(document.getElementById('fallback'),  'hidden');		
		   addClass(document.getElementById('uploadbox'), 'hidden');		
	}

	function readfiles(files) {
	    var formData = new FormData();
	    for (var i = 0; i < files.length; i++) {
	      formData.append('file', files[i]);
	    }
	
	    // now post a new XHR request
		var xhr = new XMLHttpRequest();
		xhr.open('POST', '/upload.php');
		xhr.onload = function( e ) {
			progress.value = progress.innerHTML = 100;
			if (this.responseText == "success") {
				//reload to get new file list
				location.reload();
			} else {
				//show error
				document.getElementById("error-text").innerHTML = this.responseText;
				removeClass(document.getElementById('errorbox'), 'hidden');			
			}
		};
	
		xhr.upload.onprogress = function (event) {
			if (event.lengthComputable) {
				var complete = (event.loaded / event.total * 100 | 0);
				progress.value = progress.innerHTML = complete;
			}
		}
	
		xhr.send(formData);
	}

	  holder.ondragover = function () { this.className = 'hover'; return false; };
	  holder.ondragleave = function () { this.className = 'unhover'; return false; };
	  holder.ondragend = function () { this.className = 'unhover'; return false; };
	  holder.ondrop = function (e) {
		    this.className = '';
		    e.preventDefault();
		    readfiles(e.dataTransfer.files);
	  };
	  uploadformbrowse.onchange = function(e) { 
		    var files = [uploadformbrowse.files[0]];
		    readfiles(files);
	  };
  
</script>

<div id="errorbox" class="hidden">
	<span class="error-box-symbol icon-spam" />
	<span id="error-text">No error</span>
	<span class="error-box-close  icon-cancel-circle" onclick="addClass(document.getElementById('errorbox'), 'hidden');" />
</div>

<h2>Manage existing episodes</h2>
<?php
require("config.php");
//echo entry for a single episode contained in $data
function printEntryBox($data) {
	echo "<div class=\"entrybox\"><img class=\"podcastentry\" src=\"";
	echo $podcast_baseurl.htmlentities($data["coverlink"]);
	echo "\" /><div class=\"buttons\"><br /><br /><br /><a href=\"";
	echo $podcast_baseurl.htmlentities($data["filename"]);
	echo "\" class=\"bigicon\"><span class=\"icon-link\" /></a>";
	echo "<br /><br /><a href=\"index.php?action=delete&guid=";
	echo $data["guid"];
	echo "\" class=\"bigicon\"><span class=\"icon-remove\" /></a>";
	echo"</div><h2 class=\"title\">"; 
	echo $data["title"];
	echo '</h2><div class="subtitle">Length: ';
	echo gmdate("H:i:s", $data["length"]);
	echo '&nbsp;&nbsp;&nbsp; Added at ';
	echo date(DATE_RFC2822,$data["timeadded"]);
	echo '</div><div class="description">';
	echo $data["description"];
	echo ' </div></div>';
}

//list podcasts
$db_handle  = new SQLite3($db_filename);
$db_handle->exec("CREATE TABLE IF NOT EXISTS podcast_entries (title text, guid text, description text, link text, filename text, filetype text, filesize int, timeadded int, coverlink text, length int );");
$stmt = $db_handle->prepare('SELECT * FROM podcast_entries ORDER BY timeadded DESC');
$result = $stmt->execute();

 while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	//for each episode
	printEntryBox($res);
}
?>
<h2>Help and About</h2>
Powered by <span class="icon-html5"></span>, fork me on <span class="icon-github2"></span>, licensed under GPL?, icon font by IcoMoon<span class=""></span>   
</div>
</body>
</html>
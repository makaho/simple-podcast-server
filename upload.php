<?php

require_once("config.php");
require("getid3/getid3.php");

//check for file upload error 
if ($_FILES['file']['error'] > 0) {
	echo "upload error: ";
	echo $_FILES["file"]['error'];
	echo implode(' <br> ', $_FILES['file']['error']);
	exit();
} 

//get the filename (use temporary name if really needed)
if (!empty($_FILES["file"]['name'])) {
	$filename = $_FILES['file']['name'];
} else {
	$filename = $_FILES['file']['tmp_name'];
}
//move files to temp folder
move_uploaded_file($_FILES['file']['tmp_name'], "tmp/{$filename}");

//collect meta-data
$filesize = $_FILES['file']['size'];
$filetype = $_FILES['file']['type'];
$timeadded = time();
$mediafilename = "media/{$filename}";
$guid = md5($mediafilename . $timeadded);


//process with getid3
//need: title, guid, desc, link, filename, filetype, length, timeadded, cover image
$getID3 = new getID3();
$fileinfo = $getID3->analyze("tmp/{$filename}");
getid3_lib::CopyTagsToComments($fileinfo);
$cover_link = $podcast_no_cover_image;
$lengthInSec = 0;
$title = $mediafilename;
$desc = $mediafilename;
if (isset($fileinfo['error'])) {
	$title = $mediafilename;
	$desc = $mediafilename;
	$cover_link = $podcast_no_cover_image;
	$lengthInSec = 0;
} else {
	$title = !empty($fileinfo['comments_html']['title']) ? implode(" - ", $fileinfo['comments_html']['title']) : $mediafilename;
	$artist = !empty($fileinfo['comments_html']['artist']) ? implode(" - ", $fileinfo['comments_html']['artist']) : "(no artist)";
	$album = !empty($fileinfo['comments_html']['album']) ? implode(" - ", $fileinfo['comments_html']['album']) : "(no album)";
	$fulldesc = !empty($fileinfo['comments_html']['comment']) ? implode(" - ", $fileinfo['comments_html']['comment']) : "(no description)";
	$desc = $artist . " - " . $album . " - " . $fulldesc;
	$lengthInSec = $fileinfo['playtime_seconds'];
	if (!empty($fileinfo['comments']['picture'][0])) {
		$cover_link = $mediafilename.".jpg";
		file_put_contents($cover_link, $fileinfo['comments']['picture'][0]['data']);
	}
}
$link= $podcast_link;

//create db entry
//open db and make sure, table exists
$db_handle  = new SQLite3($db_filename);
$db_handle->exec("CREATE TABLE IF NOT EXISTS podcast_entries (title text, guid text, description text, link text, filename text, filetype text, filesize int, timeadded int, coverlink text, length int );");
$stmt = $db_handle->prepare('INSERT INTO podcast_entries (title,guid,description,link,filename,filetype,filesize,timeadded,coverlink,length) VALUES (:title,:guid,:desc,:link,:filename,:filetype,:filesize,:timeadded,:coverlink,:length);');

$stmt->bindValue(':title', $title, SQLITE3_TEXT);
$stmt->bindValue(':guid', $guid, SQLITE3_TEXT);
$stmt->bindValue(':desc', $desc, SQLITE3_TEXT);
$stmt->bindValue(':link', $link, SQLITE3_TEXT);
$stmt->bindValue(':filename', $mediafilename, SQLITE3_TEXT);
$stmt->bindValue(':filetype', $filetype, SQLITE3_TEXT);
$stmt->bindValue(':filesize', intval($filesize), SQLITE3_INTEGER);
$stmt->bindValue(':timeadded', $timeadded, SQLITE3_INTEGER);
$stmt->bindValue(':coverlink', $cover_link, SQLITE3_TEXT);
$stmt->bindValue(':length', $lengthInSec, SQLITE3_INTEGER);

if (!$stmt->execute()) {
	echo "Could not add entry to database, file lost in tmp";
	echo sqlite_error_string(sqlite_last_error($db_handle)); 
	exit();
}

//move file to media directory
rename("tmp/{$filename}",$mediafilename);

//...and finally: 
if (isset($_POST['oldschool'])) {
	// for oldschool: redirect to overview list
	header("Location: http://podcast.makaho.de/index.php");
} else {
	echo "success";
}
?>
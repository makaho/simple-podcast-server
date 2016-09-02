<?

require_once("config.php");

header('Content-Type: application/rss+xml; charset=utf-8');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<rss  xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" version=\"2.0\">\n";
echo "<channel>\n";

echo "<title>";
echo $podcast_title;
echo "</title>\n";
echo "<description>";
echo $podcast_description;
echo "</description>\n";
echo "<link>";
echo $podcast_link;
echo "</link>";
echo "<language>";
echo $podcast_language;
echo "</language>\n";
echo "<copyright>";
echo $podcast_copyright;
echo "</copyright>\n";
echo "<docs>";
echo $podcast_baseurl;
echo "</docs>\n";
echo "<webMaster>";
echo $podcast_webmaster. "(Webmaster)";
echo "</webMaster>\n";
echo "<lastBuildDate>";
echo date(DATE_RFC2822);
echo "</lastBuildDate>\n";
echo "<pubDate>";
echo date(DATE_RFC2822);
echo "</pubDate>\n";

$db_handle  = new SQLite3($db_filename);

$db_handle->exec("CREATE TABLE IF NOT EXISTS podcast_entries (title text, guid text, description text, link text, filename text, filetype text, filesize int, timeadded int, coverlink text, length int );");

//get entries
$stmt = $db_handle->prepare('SELECT * FROM podcast_entries ORDER BY timeadded DESC');

$result = $stmt->execute();

 while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	//for entry
	echo "<item>\n";
		echo"<title>"; 
		echo $res["title"];
		echo"</title>\n";
		 
		echo "<link>"; 
		echo "".$podcast_baseurl;
		echo "</link>\n";
	
		echo"<guid>"; 
		echo $podcast_baseurl.$res["filename"];
		echo"</guid>\n"; 
		 
		echo"<description>";
		echo $res["description"]; 
		echo"</description>\n";
	
		echo "<enclosure url=\"";
		echo $podcast_baseurl.$res["filename"];
		echo "\" length=\"";
		echo $res['filesize'];
		echo "\" type=\"";
		echo $res["filetype"];
		echo "\"/>\n";	 
		echo"<category>Podcast</category>\n";
		echo "<itunes:duration>";
		echo $res["length"];
		echo "</itunes:duration>";
		echo "<itunes:image href=\"";
		echo $podcast_baseurl.$res["coverlink"];
		echo "\"/>\n";
		echo "<pubDate>";
		echo date(DATE_RFC2822, $res["timeadded"]);
		echo "</pubDate>\n";
		 
	echo "</item>\n";
}
 
//close everything
echo "</channel>\n</rss>\n";

?>

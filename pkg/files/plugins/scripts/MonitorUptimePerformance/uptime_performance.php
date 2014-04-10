<?php
/*
Author: Joel Pereira
Date: 05.30.2012
Description: This is a plug-in monitor that interacts with uptime through the HTTP
	interface on the Jetty port (9996).
Usage: Install via up.time Plugin Manager
*/
$hostname = "localhost";
$port     = 9996;
if ( isset($_SERVER['UPTIME_HOSTNAME']) ) {
	$hostname = trim($_SERVER['UPTIME_HOSTNAME']);
}
if ( isset($_SERVER['UPTIME_JETTY_PORT']) ) {
	$port = trim($_SERVER['UPTIME_JETTY_PORT']);
}
$url      = "http://{$hostname}:{$port}/status/performance";


//set the socket timeout to 10mins instead of the default 1min
//should help with situations where the status/performance page is slow to load on larger deployments.
ini_set('default_socket_timeout', 600);
$output = file_get_contents( trim($url) );

// if we got something from the call then we need to clean it up for up.time
if($output) {	//only if this got set by our call
	// remove all newlines so we can place them all properly later
	//$output = preg_replace("/\n/m", '', $output);
	$output = str_replace("\n", "", $output);
	$output = str_replace("<", "\n<", $output);
	
	$output_arr = preg_split("/\n/m", $output);
	$c = count($output_arr);
	//$arr_values = array();
	for ($i = 0; $i < $c; $i++){
		$line = trim($output_arr[$i]);
		// remove "<"
		$line = str_replace("<", "", $line);
		// remove any end tags "</...>"
		if ( ! preg_match("/^\//", $line) ) {
			// remove any lines without values after the tag
			if ( preg_match("/\>\d+/mi", $line) ) {
				// DEBUG: Print each line
				//print $line . "\n";
				
				// let's just print everything out to the screen
				print str_replace(">", " ", $line) . "\n";
				
				
				// now we should only have all the lines that have values, so let's split them up
				//$line_arr = preg_split("/\>/", $line);
				// now let's add it to the values array
				//array_push($arr_values, $line_arr);
			}
		}
	}
	/*
	if ( count($arr_values) > 0 ) {
		
	}
	*/
}
else {
	echo "ERROR: Could not connect to up.time URL: '{$url}'.\n";
	exit(2);
}
?>

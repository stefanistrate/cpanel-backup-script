<?php

// A PHP script to do cPanel backups. Permissions on this file should be 600.
//
// Originally developed by Vanessa Vasile (www.v-nessa.net), but modified for my
// own needs.

// Info required for cPanel access.
$cpuser = "MUST_SET_CPANEL_USERNAME";
$cppass = "MUST_SET_CPANEL_PASSWORD";
$domain = "MUST_SET_CPANEL_DOMAIN";
$skin = "paper_lantern";

// Info required for FTP host access.
$ftpmode = "passiveftp";
$ftpuser = "MUST_SET_FTP_USERNAME";
$ftppass = "MUST_SET_FTP_PASSWORD";
$ftphost = "MUST_SET_FTP_HOST";
$ftpport = "MUST_SET_FTP_PORT";
$ftpdir = "backups";

// Email to notify.
$email = "MUST_SET_EMAIL_TO_NOTIFY";

// ********** The real script work follows below. **********

// Establish the connection to cPanel.
$url = "ssl://" . $domain;
$port = 2083;
$socket = fsockopen($url, $port);
if (!$socket) {
  echo "Failed to open socket connection... Bailing out!\n";
  exit;
}

// Encode the authentication string.
$authstr = $cpuser . ":" . $cppass;
$pass = base64_encode($authstr);

// Construct the query string.
$params = ("dest=$ftpmode&email=$email&server=$ftphost&user=$ftpuser&" .
           "pass=$ftppass&port=$ftpport&rdir=$ftpdir&submit=Generate Backup");

// Make a POST request to cPanel to do the full backup.
fputs($socket,
      ("POST /frontend/" . $skin . "/backup/dofullbackup.html?" . $params .
       " HTTP/1.0\r\n"));
fputs($socket, "Host: $domain\r\n");
fputs($socket, "Authorization: Basic $pass\r\n");
fputs($socket, "Connection: Close\r\n");
fputs($socket, "\r\n");

// Grab the response even if we don't do anything with it.
while (!feof($socket)) {
  $response = fgets($socket, 4096);
}

// Close the connection.
fclose($socket);

?>

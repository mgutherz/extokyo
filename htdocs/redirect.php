<?php
/* 
    Authorization script for the custome iphone gateway by Shin Sterneck
    used by EngineX
 */

if (!isset($_SERVER["HTTP_AUTH_USER"]) || !isset($_SERVER["HTTP_AUTH_PASS"])){
        fail();
}

$username=$_SERVER["HTTP_AUTH_USER"];
$userpass=$_SERVER["HTTP_AUTH_PASS"];
$protocol=$_SERVER["HTTP_AUTH_PROTOCOL"];
$gtacUsers = array("shin.sterneck@jpn.tuv.com","ali.sarmadi@jpn.tuv.com","david.preuss@jpn.tuv.com","andreas.plaul@jpn.tuv.com");
$gtacServer = "172.16.8.32";
$smtpServer = "172.16.8.100";
$yhamaServer = "172.16.5.9";

if ($protocol=="imap") {
        $backend_port=143;
} elseif ($protocol=="smtp") {
	$backend_port=25;
	if ($username != "tuv" || $userpass != "sending") fail();
} else {
	fail();
}

$server = getServerForUser($username,$protocol);
$port = $backend_port;

pass($server,$port);

// this will both check username existance and associated server

function getServerForUser($user,$protocol){
	global $gtacUsers,$gtacServer,$smtpServer;

	if ($protocol == "smtp") return $smtpServer;

	if (in_array($user,$gtacUsers)){
		return $gtacServer;
	} else {
		fail();
	}
}

function pass($server,$port){
	header("Auth-Status: OK");
	header("Auth-Server: $server");
	header("Auth-Port: $port");
	exit;
}

function fail(){
	header("Auth-Status: Invalid login or password");
        exit;
}

?>

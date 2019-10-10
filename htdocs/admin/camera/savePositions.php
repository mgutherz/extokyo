<?php
$file = dirname(__FILE__) . '/positions.txt';

if (!($fd = fopen($file, 'w'))) {
	header('x-error', "Couldn't write to $file: $!");
	exit;
}

$positions = array();
$i = 0;
while (array_key_exists("pos$i", $_REQUEST)) {
	echo "<br />\nrequest: ".print_r($_REQUEST["pos$i"],1)."<br />\n";
	$p = split(',', $_REQUEST["pos$i"], 5);
	echo "<br />\np: ".print_r($p,1)."<br />\n";
	$p[4] = "'".urldecode($p[4])."'";
	$positions[] = '['.join(',', $p).']';
	$i++;
}

fwrite($fd, "positions = new Array(".join(',', $positions).");\n");
echo "<br />\npositions = new Array(".join(',', $positions).");\n";

fclose($fd);

?>
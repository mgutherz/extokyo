<?
$params = array('x', 'y', 'w', 'h', 'filename', 'name', 'month', 'day');
	
foreach ($params as $param)
	if (!array_key_exists($param, $_REQUEST))
		die("Missing parameter: $param");

$db = new SQLite3($DBNAME);

require_once('image_cropper_scaled.php');

if ($_REQUEST['w'] > 0) { # crop requested and scale down
	$x = $_REQUEST['x'];
	$y = $_REQUEST['y'];
	$w = $_REQUEST['w'];
	$h = $_REQUEST['h'];
} else { # scale the whole image
	$x = 0;
	$y = 0;
	$w = 0;
	$h = 0;
}

if ($error = cropImage($BIRTHDAYDIR.$_REQUEST['filename'], $BIRTHDAYDIR.$_REQUEST['filename'], $x, $y, $w, $h, $IMGWIDTH, $IMGHEIGHT)) {
	echo "<p>Error formatting image: $error</p>";
} else {
	$db->exec(sprintf("INSERT INTO birthday(month, day, name, picture) VALUES(%d, %d, '%s', '%s')", $_REQUEST['month'], $_REQUEST['day'], $db->escapeString($_REQUEST['name']), $db->escapeString($_REQUEST['filename']))) || die($db->lastErrorMsg());
}

include TEMPLATEDIR . '/admin/birthdays/list.inc';
?>
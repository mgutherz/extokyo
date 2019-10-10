<?
$db = new SQLite3($DBNAME);

# use current month as fallback
$month = array_key_exists('month',$_REQUEST) ? $_REQUEST['month'] : date('m');

# get birthday id
if (array_key_exists('id', $_REQUEST))
	$id = $_REQUEST['id'];

# construct array of months
$months = array();
for ($i=1; $i<=12; $i++) {
	$months[$i] = date('F', mktime(1, 1, 1, $i, 1));
}

if ($act == 'edit') {
	include 'birthdayAdminEdit.inc';
} elseif ($act == 'editdone') {
	$db->exec(sprintf("UPDATE birthday SET month=%d, day=%d, name='%s' WHERE rowid=%d", $_REQUEST['month'], $_REQUEST['day'], $db->escapeString($_REQUEST['name']), $id)) || die($db->lastErrorMsg());

	include 'birthdayAdminBrowse.inc';
} elseif ($act == 'new') {
	include 'birthdayAdminNew.inc';
} elseif ($act == 'newdone') {
	require_once('image_cropper_scaled.php');
	if (array_key_exists('w', $_REQUEST) && $_REQUEST['w'] > 0) { # crop requested and scale down
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
		include 'birthdayAdminBrowse.inc';
	} else {
		$db->exec(sprintf("INSERT INTO birthday(month, day, name, picture) VALUES(%d, %d, '%s', '%s')", $_REQUEST['month'], $_REQUEST['day'], $db->escapeString($_REQUEST['name']), $db->escapeString($_REQUEST['filename']))) || die($db->lastErrorMsg());
		header('Location: '.$_SERVER['SCRIPT_NAME']."?act=browse&month=$month");
		exit;
	}
} elseif ($act == 'crop') {
	include 'birthdayAdminCrop.inc';
} else { # browse or delete
	if ($act == 'del')
 		$db->exec(sprintf("DELETE FROM birthday WHERE rowid = %d", $id)) || die($db->lastErrorMsg());

	include 'birthdayAdminBrowse.inc';
}

?>

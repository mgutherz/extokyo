<?
# get birthday id
if (array_key_exists('id', $_REQUEST))
	$id = $_REQUEST['id'];
else
	die("Missing parameter: id");

$db = new SQLite3($DBNAME);
$db->exec(sprintf("DELETE FROM birthday WHERE rowid = %d", $id)) || die($db->lastErrorMsg());

include TEMPLATEDIR . '/admin/birthdays/list.inc';
?>

<?
	$db = new SQLite3($DBNAME);
	$params = array('id', 'month', 'day', 'name') ;
	
	foreach ($params as $param)
		if (!array_key_exists($param, $_REQUEST))
			die("Missing parameter: $param");
	
	$db->exec(sprintf("UPDATE birthday SET month=%d, day=%d, name='%s' WHERE rowid=%d", $_REQUEST['month'], $_REQUEST['day'], $db->escapeString($_REQUEST['name']), $_REQUEST['id'])) || die($db->lastErrorMsg());

	include TEMPLATEDIR . '/admin/birthdays/list.inc';
?>
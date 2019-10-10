<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>EX German Snack Bar</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<?
require_once 'config.php';
?>
<style type="text/css">
td.spaced {vertical-align: top; padding: <?=$IMGSPACING?>px;}
.name {font-size: <?=$NAMESIZE?>px;}
</style>
</head>
<body>
<?
$db = new SQLite3($DBNAME, SQLITE3_OPEN_READONLY);
$result = $db->query("SELECT name, picture FROM birthday WHERE month = cast(strftime('%m','now') as integer) AND day = cast(strftime('%d', 'now') as integer) ORDER BY name");
if ($result === false)
	die($db->lastErrorMsg());

$bdays = array();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
	$bdays[] = $row;
}

if (count($bdays) > 0) {
	echo "<table>\n";
	$i=0;
	foreach ($bdays as $bday) {
		if (($i % $MAXCOLS) == 0) {
			echo "<tr>\n";
		}
		?>
<td class="spaced"><div class="name"><?=$bday['name']?></div><img src="<?=$BIRTHDAYURI?><?=$bday['picture']?>" /></td>
<?
		if ((++$i % $MAXCOLS) == 0) {
			echo "</tr>\n";
		}
	}
	echo "</table>";
} else {
	echo "No birthdays for today, come back tomorrow!";
}
?>
</body>
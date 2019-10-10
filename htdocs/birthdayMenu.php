<?
require_once 'config.php';

$db = new SQLite3($DBNAME, SQLITE3_OPEN_READONLY);
$result = $db->query("SELECT name FROM birthday WHERE month = cast(strftime('%m','now') as integer) AND day = cast(strftime('%d', 'now') as integer) ORDER BY name");
if ($result === false)
	die($db->lastErrorMsg());

$bdays = array();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
	$bdays[] = $row['name'];
}

if (count($bdays) > 0) {
	echo "<div id=\"birthdayCheers\"><p class=\"red\">";
	foreach ($bdays as $name) {
		echo "Hallo $name!<br />";
	}
	?>
</p>
<p class="yellow">
Happy Birthday!<br />
<!--O-tanjobi omedeto gozaimasu!<br />-->
<!--お誕生日おめでとうございます<br />-->
ハッピーバースデー！<br />
Herzlichen Glueckwunsch<br />
zum Geburtstag!</p>
<p class="red">
Your "old" Ex-Friends!!!<br />
</p>
<a style="color: #ffcc00; font-weight: normal; font-style: normal; font-family: Arial;" href="#" onClick="window.open('/birthdayPics.php','birthday','width=<?= min($MAXCOLS, count($bdays)) * ($IMGWIDTH + 2*$IMGSPACING) + 20?>,height=<?= ceil(count($bdays) / $MAXCOLS) * ($IMGHEIGHT + 2*$IMGSPACING + $NAMESIZE) + 20?>,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,copyhistory=yes,resizable=yes')">[click for birthday picture<?=count($bdays)>1?'s':''?>!]</a>
</div>

<?
$db->close();
}
?>
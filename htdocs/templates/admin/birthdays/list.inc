<?
$db = new SQLite3($DBNAME, SQLITE3_OPEN_READONLY);

# use current month as fallback
$month = array_key_exists('month',$_REQUEST) ? $_REQUEST['month'] : date('m');

# retrieve birthdays for this month
$result = $db->query(sprintf("SELECT rowid, day, name, picture FROM birthday WHERE month = %d ORDER BY day, name", $month));
if ($result === false)
	die($db->lastErrorMsg());

# construct array of months
$months = array();
for ($i=1; $i<=12; $i++) {
        $months[$i] = date('F', mktime(1, 1, 1, $i, 1));
}

?>
<div style="top: 0; bottom: 0; text-align: center;">

<div class="controls">
Month: <select onChange="window.location='<?=$_SERVER['SCRIPT_NAME']?>?month='+this.value;" name="month">
<?
foreach ($months as $iMon => $nameMon) {
	?><option value="<?=$iMon?>"<?=$month==$iMon?' selected':''?>><?=$nameMon?></option><?
}
?>
</select>
<a href="<?=$_SERVER['SCRIPT_NAME']?>?act=new&month=<?=$month?>" alt="Add"><img src="saveas.png" /> Add new birthday</a>
</div>

<table id="bdayList">
<tr><th>Day</th><th>Name</th><th>Picture</th><th>Modify</th></tr>
<?
while ($v = $result->fetchArray(SQLITE3_ASSOC)) {
	?><tr><td><?=$v['day']?></td><td class="border"><?=$v['name']?></td><td><a href="<?=$BIRTHDAYURI?><?=$v['picture']?>" target="_new"><img class="photo" src="<?=$BIRTHDAYURI?><?=$v['picture']?>" /></a></td><td><a onClick="return confirm('Really delete <?=$v['name']?>?');" href="<?=$_SERVER['SCRIPT_NAME']?>?month=<?=$month?>&act=del&id=<?=$v['rowid']?>" alt="Delete"><img src="editdelete.png" /></a><a href="<?=$_SERVER['SCRIPT_NAME']?>?act=edit&id=<?=$v['rowid']?>" alt="Edit"><img src="edit.png" /></a></td></tr>
<?
}
$db->close();
?>
</table>

</div>

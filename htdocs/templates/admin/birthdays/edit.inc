<?
$db = new SQLite3($DBNAME, SQLITE3_OPEN_READONLY);

# get birthday id
if (array_key_exists('id', $_REQUEST))
	$id = $_REQUEST['id'];
else
	die("Missing parameter: id");



$result = $db->querySingle(sprintf("SELECT month, day, name, picture FROM birthday WHERE rowid = %d", $id), true);
if ($result === false)
	die($db->lastErrorMsg());
$db->close();

$name = $result['name'];
$picture = $result['picture'];
$month = $result['month'];
$day = $result['day'];


# construct array of months
$months = array();
for ($i=1; $i<=12; $i++) {
	$months[$i] = date('F', mktime(1, 1, 1, $i, 1));
}

?>
<script language="javascript">
var monthDays = new Array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
function setDays() {
	var monsel = document.getElementById('monthSelector');
	var daysel = document.getElementById('daySelector');
	// add missing days for this month
	for (i=daysel.options.length+1; i<=monthDays[monsel.selectedIndex]; i++) {
		daysel.options[daysel.options.length] = new Option(i, i);
	}
	// remove extra days for this month
	for (i=daysel.options.length-1; i>=monthDays[monsel.selectedIndex]; i--) {
		daysel.remove(i);
	}
}

function checkForm() {
	if (document.getElementById('name').value=='') {
		alert('Please type in the name of the person');
		document.getElementById('name').focus();
		return false;
	}
	return true;
}
</script>

<form name="newBirthday" method="POST" action="<?=$_SERVER['SCRIPT_NAME']?>?act=editdone" enctype="multipart/form-data" onSubmit="return checkForm();">
<input type="hidden" name="id" value="<?=$id?>" />
<table align="center">
<tr><td colspan="2" align="center"><img src="<?=$BIRTHDAYURI.$picture?>" /></td></tr>
<tr><td align="right">Name: </td><td><input type="text" name="name" id="name" value="<?=$name?>" /></td></tr>
<tr><td align="right">Birthday: </td><td>
<select name="day" id="daySelector">
<?
for ($i=1; $i<=29; $i++) {
	?><option value="<?=$i?>"><?=sprintf('%02d',$i)?></option>
<?
}
?>
</select>
<select name="month" id="monthSelector" onChange="setDays();">
<?
foreach ($months as $iMon => $nameMon) {
	?><option value="<?=$iMon?>"<?=$month==$iMon?' selected="true"':''?>><?=$nameMon?></option><?
}
?>
</select>
<script language="javascript">
setDays();
document.getElementById('daySelector').selectedIndex = <?=($day - 1)?>;
</script>

</td></tr>
<tr><td colspan="2" align="center"><input type="button" value="Cancel" onClick="window.location.href='<?=$_SERVER['SCRIPT_NAME']?>?month=<?=$month?>';" />&nbsp;&nbsp;<input type="submit" value="Finish" /></td></tr>
</table>
</form>
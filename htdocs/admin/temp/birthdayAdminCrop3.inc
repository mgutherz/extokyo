<?
$name = $_REQUEST['name'];
$month = $_REQUEST['month'];
$day = $_REQUEST['day'];

$ul_filename = pathinfo($_FILES['picture']['name']);

if ($_FILES['image']['error'] !=0 ) {
	die("Error uploading image: ".$_FILES['image']['error']."(click Back to try again)");
}

if (!preg_match('/^\w+[\s*\w]?\w+$/', $name)) {
	die("Invalid name: '$name' (click Back to fix it)");
}

# find an unused filename
$i=0;
do {
	$filename = strtolower($name).($i++?$i:'').'.'.$ul_filename['extension'];
} while (@lstat($BIRTHDAYDIR.$filename));

if (move_uploaded_file($_FILES['picture']['tmp_name'], $BIRTHDAYDIR . $filename)) {
} else {
	die("Error writing to $BIRTHDAYDIR$filename");
}

?>

<table align="center" style="padding: 0px;">
<tr style="padding: 0px;"><td style="padding: 0px;">
<div id="testWrap"><img src="../birthday/<?=$filename?>" id="testImage" width="425" height="640" /></div>
</td></tr>
<tr><td><div class="name"><b><?=$_REQUEST['name']?></b></div></td></tr><tr><td class="border"><ol><li>Click the opposite diagonal corners of the area you want to keep</li><li>To cancel the selection click the image again and try again</li><li>Click Finish to save</li></ol>Just click Finish if you want to keep the whole picture</td></tr>
<tr><td>

<form id="cropForm" method="POST" action="<?=$_SERVER['PHP_SELF']?>?act=newdone" onSubmit="setArea();">
<input type="hidden" name="month" value="<?=$month?>" />
<input type="hidden" name="day" value="<?=$day?>" />
<input type="hidden" name="name" value="<?=$name?>" />
<input type="hidden" name="filename" value="<?=$filename?>" />
<input type="hidden" name="x" value="" />
<input type="hidden" name="y" value="" />
<input type="hidden" name="w" value="" />
<input type="hidden" name="h" value="" />
<input type="button" value="Cancel" onClick="window.location.href='/internal/birthdayAdmin.php';" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Finish" />
</form>

</td></tr>
</table>

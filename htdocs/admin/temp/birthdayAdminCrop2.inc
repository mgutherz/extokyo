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

<style type="text/css">
	#imgJSselbox{
	position: absolute;
	visibility: hidden;
	width: 0px;
	height: 0px;
	border: 1px solid #006;
	color: #fff;
	background-image: url(imgcrop/selection_area.gif);
	z-index: 500;
}
	#imgJScross1 {
	position: absolute;
	visibility: hidden;
	width: 63px;
	height: 63px;
	background-image: url(imgcrop/cross.gif);
	z-index: 501;
}
	#imgJScross2 {
	position: absolute;
	visibility: hidden;
	width: 63px;
	height: 63px;
	background-image: url(imgcrop/cross.gif);
	z-index: 501;
}
</style>
<script src="imgcrop/image_cropper.js" type="text/javascript"></script>
<table align="center" style="padding: 0px;">
<tr style="padding: 0px;"><td style="padding: 0px;">
<p><img src="../birthday/<?=$filename?>" id="image" onClick="getImageCropSelectionPoint('image',event);" /></p>
<div id="imgJSselbox"></div><div id="imgJScross1"></div><div id="imgJScross2"></div>
</td></tr>
<tr><td><div class="name"><b><?=$_REQUEST['name']?></b></div></td></tr><tr><td class="border"><ol><li>Click the opposite diagonal corners of the area you want to keep</li><li>To cancel the selection click the image again and try again</li><li>Click Finish to save</li></ol>Just click Finish if you want to keep the whole picture</td></tr>
<tr><td>

<script language="javascript">
function setArea() {
	var form = document.getElementById('cropForm');
	// these 2 lines are in case the area wasn't selected from top left to bottom right.
	if (x2!==undefined) { // checked if there is a selection
		form.x.value = Math.min(x1, x2) - docx;
		form.y.value = Math.min(y1, y2) - docy;
		form.w.value = Math.abs(x2 - x1);
		form.h.value = Math.abs(y2 - y1);
	}
	return true;
}
</script>

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

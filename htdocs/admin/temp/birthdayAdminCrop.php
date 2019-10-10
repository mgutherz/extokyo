<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>EX German Snack Bar</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<style type="text/css">
.border { border: 1px solid #cfcfcf; padding: 5px;}
</style>
<script src="cropper/lib/prototype.js" type="text/javascript"></script>
<script src="cropper/lib/scriptaculous.js?load=builder,dragdrop" type="text/javascript"></script>
<script src="cropper/cropper.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">


function onEndCrop(coords, dimension) {
}

</script>
</head>
<body onload="init();">
<table style="width: 100%; height: 100%;">
<tr><td style="height: 164px; width: 180px; vertical-align: top;">
<?
include '../header.inc';
?>
</td>
<td rowspan="3" style="height: 100%; text-align: center; vertical-align: top;"><!-- right side -->
<?
require_once '../birthday.inc';
include 'birthdayAdminCrop.inc';
?>
</td><!-- end of right side -->
</tr>
<tr><td style="vertical-align: top;">
<?
include '../menu.inc';
?>
</td></tr>
<tr><td style="vertical-align: bottom;">
<?
include '../sponsor.inc';
?>
</td></tr>

</table></body></html>
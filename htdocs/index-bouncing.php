<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>EX German Snack Bar</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script language="JavaScript">
// elements: name, actual position, bounces remaining, next bounce height, speed
var animationObjects = new Array(['anim1', -400, 5, -20, 10], ['anim2', -1500, 7, -50, 15], ['anim3', -3000, 7, -60, 20]);
var interval = 10;
var minspeed = 5;
var remaining = animationObjects.length;
function animate() {
	var tmp;
	for (var i = 0; i<animationObjects.length; i++) {
		if (animationObjects[i][2] > 0) {
			newpos = animationObjects[i][1] - (Math.pow(-1, animationObjects[i][2]) * animationObjects[i][4]);
			if (((newpos >= 0) && ((animationObjects[i][2] % 2) == 1)) ||((newpos <= animationObjects[i][3]) && ((animationObjects[i][2] % 2) == 0)))  {
				animationObjects[i][2] -= 1; // bounces remaining
				animationObjects[i][3] += 20; // bounce height
				// animationObjects[i][4] = Math.min(animationObjects[i][4] - 5, minspeed); // speed
			}
			animationObjects[i][1] = newpos;
			if ((newpos == 0) && (animationObjects[i][2] < 0)) {
				remaining--;
			}
/*		

		if (newpos <= 0) { // direction change
			animationObjects[i][2] += 1;
		}

		if (newpos < 0) {



			animationObjects[i][1] = newpos;
		} else {
			animationObjects.splice(i,1);
		}
*/
			document.getElementById(animationObjects[i][0]).style.top = newpos+'px';
		}
	}
	if (remaining > 0)
		setTimeout("animate()", interval);
}
</script>
</head>
<body onload="animate()">
<table style="width: 100%; height: 100%;"><tr>
<tr><td style="height: 164px; width: 180px;">
<?
include 'header.inc';
?>
</td>
<td rowspan="3" style="height: 100%; text-align: center; vertical-align: middle;"><!-- right side -->
	<table class="flag" style="width: 650px; height: 390px; margin-left: auto; margin-right: auto;" >
	<tr><td class="flagrow bigflagrow flagrow1"><div id="anim1" class="anim anim1">EX</div></td></tr>
	<tr><td class="flagrow bigflagrow flagrow2"><div id="anim2" class="anim anim2">German Snack</div></td></tr>
	<tr><td class="flagrow bigflagrow flagrow3"><div id="anim3" class="anim anim3">Bar</div></td></tr>
	</table>
</td><!-- end of right side -->
</tr>
<tr><td>
<?
include 'menu.inc';
?>
</td></tr>
<tr><td style="height: 60px;">
<table>
<tr><td style="vertical-align: bottom"><a href="http://www.tuv.com/jp/en/products_and_services.html"><img src="tuvnew.png" /></a></td><td style="vertical-align: bottom; padding-bottom: 3px;">System powered by<br /><a href="http://www.tuv.com/jp/en/products_and_services.html">T&Uuml;V Rheinland Group</a></td></tr>
</table>
</td></tr>

</table>
</body></html>

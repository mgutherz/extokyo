<script language="JavaScript">
var animationObjects = new Array(['anim1', 1000],['anim2', 2000],['anim3', 3000]);
var speed = 10;
var interval = 10;
function animate() {
	var tmp;
	for (var i = 0; i<animationObjects.length; i++) {
		newpos = Math.max(animationObjects[i][1] - speed, 0);
		document.getElementById(animationObjects[i][0]).style.bottom = newpos+'px';
		if (newpos > 0) {
			animationObjects[i][1] = newpos;
		} else {
			// this fixes IE's wrong interpretation of the meaning of relative positioning
			document.getElementById(animationObjects[i][0]).style.position = 'static';
			animationObjects.splice(i,1);
		}
	}
	if (animationObjects.length > 0)
		setTimeout("animate()", interval);
}

setTimeout("window.location.href = '/webcam.php'", 60000);
</script>

<table style="width: 100%; height: 100%;">
<tr><td style="height: 20%;">
<table class="boxNote" style="margin: 20px; margin-left: auto;"><tr><td class="boxNote">Business Hours:<br />Weekdays: 6:00pm ~ 2:00am<br />Saturday: 5:00pm ~ 2:00am<br />Closed on Sundays</td></tr></table>
</td></tr>
<tr><td>
<table class="flag bigflag" style="margin-left: auto; margin-right: auto;">
<tr><td><div id="anim1">EX</div></td></tr>
<tr><td><div id="anim2">German Snack</div></td></tr>
<tr><td><div id="anim3">Bar</div></td></tr>
</table>
</td></tr>
<tr><td style="height: 20%;">
</td></tr>
</table>

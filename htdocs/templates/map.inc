<?
$mapkey = 'ABQIAAAA3IMuifpkMdJ10lO4vppsnxRog_pLNXlGfpC9OrX0aG1P18tQlhQomd7nORAKtyOFkAh-gdtyirM84w';
$mapkey = 'AIzaSyC3i8vs1JfnHv8V2w8P8FR1kRNOL_oayBU';

# development key for dev.ex-tokyo.com
if (preg_match('/^dev/', $_SERVER['SERVER_NAME']))
	$mapkey='ABQIAAAA3IMuifpkMdJ10lO4vppsnxRMIE6uugFlrS8fImFosNcElx434RQPIBpcoqVZqLUwVJaAJ-TzdsmRxw';
	$mapkey='AIzaSyC3i8vs1JfnHv8V2w8P8FR1kRNOL_oayBU';
?>
<script src="http://maps.google.com/maps?hl=en&amp;file=api&amp;v=2&amp;key=<?=$mapkey?>" type="text/javascript"></script>
<script type="text/javascript">
function map_init() {
	if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("map"));
		var latlng = new GLatLng(35.663736, 139.729791);
		var icon = new GIcon();
		icon.image = "/mapflag.png";
		icon.iconAnchor = new GPoint(0,32);
		icon.infoWindowAnchor = new GPoint(16,0);
		icon.iconSize = new GSize(32,32);
		var marker = new GMarker(latlng, icon, true);
		map.setCenter(latlng, 13);
		map.setZoom(16);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.addOverlay(marker);
		marker.openInfoWindowHtml('<div style="color: #000000; text-align: left; font-family: Arial; font-size: 12px;">Horst & Hiroshi<br />Verde Roppongi 101<br />7-16-11 Roppongi, Minato-Ku Tokyo, 106-0032</div>');
	} else {
		document.getElementById("map").innerHTML = 'Sorry, your browser is not compatible';
	}
}
</script>


<div class="title" align="center">How to find us</div>
<div id="map" style="width: 80%; height: 80%; vertical-align:middle; margin-left: auto; margin-right: auto; border: 1px solid #800000"></div>

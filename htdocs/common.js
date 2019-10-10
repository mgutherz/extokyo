function updateAjax(id, uri) {
	var xmlHttp;
	try {
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	} catch (e) {
		// Internet Explorer
		try {
			xmlHttp=new ActiveXObject('Msxml2.XMLHTTP');
		} catch (e) {
			try {
				xmlHttp=new ActiveXObject('Microsoft.XMLHTTP');
			} catch (e) {
				alert('Your browser is not supported');
				return false;
			}
		}
	}
	xmlHttp.onreadystatechange=function() {
		if(xmlHttp.readyState==4) {
			if (xmlHttp.status == 200) {
				document.getElementById(id).innerHTML=xmlHttp.responseText;
			} else {
				alert('Error loading "'+uri+'". Status: '+xmlHttp.status);
			}
		}
	}
	xmlHttp.open('GET', uri+'?seq='+(++seq)+'&rnd='+Math.random(), true);
	xmlHttp.send(null);
}

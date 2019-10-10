/*
Written by Alan Mizrahi on 2009-02-20
*/

function getProperty(obj, prop) {
	if (typeof(obj.currentStyle)!='undefined') {
		return eval(obj.currentStyle.prop);
	} else {
		return window.getComputedStyle(obj, '').getPropertyValue(prop);
	}
}

var docx=docy=undefined; // x,y position of the image, relative to the document
var x1=y1=undefined; // x,y position of first click, relative to the document
var x2=y2=undefined; // x,y position of second click, relative to the document

function getImageCropSelectionPoint(elem, event) {
	var xoff = yoff = 0; // temporary variables to calculate page offset
	
	if (x2!==undefined) { // third click, clear selection
		x1=y1=undefined;
		x2=y2=undefined;
		document.getElementById('imgJScross1').style.visibility='hidden';
		document.getElementById('imgJScross2').style.visibility='hidden';
		document.getElementById('imgJSselbox').style.visibility='hidden';
		return;
	}
	
	if (docx===undefined) { // get image position relative to document, only once
		obj=document.getElementById(elem);
		docx=obj.offsetLeft;
		docy=obj.offsetTop;
		while (obj.offsetParent) {
			obj = obj.offsetParent;
			docx+=obj.offsetLeft;
			docy+=obj.offsetTop;
		}
	}

	if (typeof(window.pageXOffset)=='undefined') { // calculate the current page offset (to compensate for scrolling)
		// MSIE browsers
		xoff=document.body.scrollLeft;
		yoff=document.body.scrollTop;
	} else {
		// others
		xoff=window.pageXOffset;
		yoff=window.pageYOffset;
	}
	if (x1===undefined) { // first click
		x1 = event.clientX + xoff;
		y1 = event.clientY + yoff;
		// show first cross
		var cross1 = document.getElementById('imgJScross1');
		cross1.style.left = (x1 - 31) +'px';
		cross1.style.top = (y1 - 31) +'px';
		cross1.style.visibility = 'visible';
	} else { // second click
		x2 = event.clientX + xoff;
		y2 = event.clientY + yoff;
		// show second cross
		var cross2 = document.getElementById('imgJScross2');
		cross2.style.left = (x2 - 31) +'px';
		cross2.style.top = (y2 - 31) +'px';
		cross2.style.visibility = 'visible';
		// show the selection
		selElem = document.getElementById('imgJSselbox');
		selElem.style.width = Math.abs(x2-x1)+'px';
		selElem.style.height = Math.abs(y2-y1)+'px';
		selElem.style.left = Math.min(x1,x2)+'px';
		selElem.style.top = Math.min(y1,y2)+'px';
		selElem.style.visibility = 'visible';
	}

}


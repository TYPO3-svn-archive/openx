var openxURL = new Object();

function loadIframe() {
	for (var i in openxURL) {
		document.getElementById(i).src = openxURL[i];
	}
}

function addEvent(obj, evType, fn){ 
	if (obj.addEventListener){ 
		obj.addEventListener(evType, fn, false); 
		return true; 
	} else if (obj.attachEvent){ 
		var r = obj.attachEvent("on"+evType, fn); 
		return r; 
	} else { 
		return false; 
	} 
}

addEvent(window, 'load', loadIframe);

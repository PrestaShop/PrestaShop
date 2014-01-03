if (navigator.userAgent.match(/Android/i)) {
	var viewport = document.querySelector("meta[name=viewport]");
	viewport.setAttribute('content', 'initial-scale=1.0,maximum-scale=1.0,user-scalable=0,width=device-width,height=device-height');
}
if (navigator.userAgent.match(/Android/i)) {
	window.scrollTo(0,1);
}
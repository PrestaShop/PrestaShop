
function socialsharing_twitter_click(message) {
	if (message === undefined)
		message = location.href;
	window.open('https://twitter.com/intent/tweet?text=' + message, 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
}

function socialsharing_facebook_click(message) {
	window.open('http://www.facebook.com/sharer.php?u=' + location.href, 'sharer', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_google_click(message) {
	window.open('https://plus.google.com/share?url=' + location.href, 'sharergplus', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_pinterest_click(message) {
	window.open('http://www.pinterest.com/pin/create/button/?url=' + location.href, 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}

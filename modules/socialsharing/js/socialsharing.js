
function socialsharing_twitter_click(message) {
	if (message === undefined)
		message = socialsharing_prepare_url( location.href );
	window.open('https://twitter.com/intent/tweet?text=' + message, 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
}

function socialsharing_facebook_click(message) {
	window.open('http://www.facebook.com/sharer.php?u=' + socialsharing_prepare_url( location.href ), 'sharer', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_google_click(message) {
	window.open('https://plus.google.com/share?url=' + socialsharing_prepare_url( location.href ), 'sharergplus', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_pinterest_click(message) {
	window.open('http://www.pinterest.com/pin/create/button/?url=' + socialsharing_prepare_url( location.href ), 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_prepare_url(href) {
	var find = '&';
	var re = new RegExp(find, 'g');

	return href.replace(re, '%26');
}
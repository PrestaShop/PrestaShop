function highdpi_init() {
	if($('.replace-2x').css('font-size') == "1px") {
		
		var els = $("img.replace-2x").get();
		for(var i = 0; i < els.length; i++) {
			src = els[i].src;
			extension = src.substr( (src.lastIndexOf('.') +1) );
			src = src.replace("."+extension, "2x."+extension);
			
			var img = new Image();
			img.src = src;
			img.height != 0 ? els[i].src = src : els[i].src = els[i].src;
		}
	}
}
$(document).ready(function() {
	highdpi_init();
});


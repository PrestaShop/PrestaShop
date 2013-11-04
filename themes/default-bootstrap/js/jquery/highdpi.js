function highdpi_init() {
	if($('.replace-2x').css('font-size') == "1px") {
		
		var els = $("img.replace-2x").get();
		for(var i = 0; i < els.length; i++) {
			var src = els[i].src;
			src = src.replace(".png", "2x.png");
			src = src.replace(".jpg", "2x.jpg");
			src = src.replace(".gif", "2x.gif");
			els[i].src = src;
		}
	}
}
$(document).ready(function() {
	
	highdpi_init();
	
	$(".replace-2x").each( function () 
		{
			$.ajax({
			url:$(this).attr('src'),
			type:'HEAD',
			error:function(){		
			
				if($('.replace-2x').css('font-size') == "1px") {
				
					var els = $("img.replace-2x").get();
					for(var i = 0; i < els.length; i++) {
						var src = els[i].src;
						src = src.replace("2x.png", ".png");
						src = src.replace("2x.jpg", ".jpg");
						src = src.replace("2x.gif", ".gif");
						els[i].src = src;
					}
				}
			},
			success:function(){}
		});
	});
});


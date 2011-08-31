$(function(){
	
	if (!homeslider_speed == undefined)
		var homeslider_speed = 300;
	if (!homeslider_pause == undefined)
		var homeslider_pause = 6000;
	
	  $('#homeslider').bxSlider({
	    infiniteLoop: true,
	    hideControlOnEnd: true,
	    pager: true,
	    autoHover: true,
	    auto: true,
	    speed: homeslider_speed,
	    pause: homeslider_pause
	  });
});
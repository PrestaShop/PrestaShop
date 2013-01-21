{if $page_name == 'index'}
<script type="text/javascript" src="{$this_path}js/nivo.slider.js"></script>
<div id="tmnivoslider"> 	
    <div id="slider">
	{foreach from=$xml->link item=home_link name=links}
		<a href="{$home_link->url}"><img src="{$this_path}{$home_link->img}" alt="" title="#htmlcaption{$smarty.foreach.links.iteration}" /></a>
	{/foreach}
	</div>
	{foreach from=$xml->link item=home_link name=links}
		<div id="htmlcaption{$smarty.foreach.links.iteration}" class="nivo-html-caption">
			<h2>{$home_link->$field1}</h2>
			<h3>{$home_link->$field2}</h3>
			<h4>{$home_link->$field3}</h4>
			<h5>{$home_link->$field4}</h5>
			<a class="slide_btn" href="{$home_link->url}">{$home_link->$field5}</a>
		</div>
	{/foreach}
</div>
<script type="text/javascript">
{literal}
$(window).load(function() {
	$('#slider').nivoSlider({
		effect:'fade', //Specify sets like: 'fold,fade,sliceDown'
		slices:10,
		animSpeed:500, //Slide transition speed
		pauseTime:5000,
		startSlide:0, //Set starting Slide (0 index)
		directionNav:false, //Next & Prev
		directionNavHide:false, //Only show on hover
		controlNav:true, //1,2,3...
		controlNavThumbs:false, //Use thumbnails for Control Nav
      	controlNavThumbsFromRel:false, //Use image rel for thumbs
		controlNavThumbsSearch: '.jpg', //Replace this with...
		controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
		keyboardNav:true, //Use left & right arrows
		pauseOnHover:true, //Stop animation while hovering
		manualAdvance:false, //Force manual transitions
		captionOpacity:1.0, //Universal caption opacity
		beforeChange: function(){},
		afterChange: function(){},
		slideshowEnd: function(){} //Triggers after all slides have been shown
	});
});
{/literal}
</script>
{/if}
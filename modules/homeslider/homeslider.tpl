<!-- Module HomeSlider --> 
{if isset($homeslider)}
<script type="text/javascript">
	var homeslider_loop = true;
	var homeslider_speed = {$homeslider.speed};
	var homeslider_pause = {$homeslider.pause};
</script>
{/if}
{if isset($homeslider_slides)}
<ul id="homeslider">
{foreach from=$homeslider_slides item=slide}
	{if $slide.active}
		<li><a href="{$slide.url}" title="{$slide.description}"><img src="modules/homeslider/images/{$slide.image}" alt="{$slide.legend}" title="{$slide.description}" height="{$homeslider.height}" width="{$homeslider.width}"></a></li>
	{/if}
{/foreach}
</ul>
{/if}
<!-- /Module HomeSlider -->

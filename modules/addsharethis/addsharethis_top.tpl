<link rel="stylesheet" type="text/css" href="{$module_dir}css/addsharethis.css"/>
{if isset($addsharethis_data)}
<div class="addsharethis topaddsharethis">
<div class="addsharethisinner">
	{if isset($addsharethis_data.twitter)}
		{$addsharethis_data.twitter}
	{/if}
	{if isset($addsharethis_data.google)}
		{$addsharethis_data.google}
	{/if}
	{if isset($addsharethis_data.pinterest)}
		{$addsharethis_data.pinterest}
	{/if}
	{if isset($addsharethis_data.facebook)}
		{$addsharethis_data.facebook}
	{/if}
</div>
</div>
{/if}
<script type="text/javascript">
$(function(){
    $(".addsharethis").addClass("loader");
$(window).load(function() {
	 $(".addsharethis").removeClass("loader");
$(".addsharethisinner").show(400);
}); 
});
$(function(){
		$(".addsharethis .sharebtn").click(function(){
			$(this).find("img").show("medium");
		})
	});
</script>
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">{literal}stLight.options({publisher: "{/literal}{$conf_row}{literal}", doNotHash: false, doNotCopy: false, hashAddressBar: false});{/literal}</script>

<link rel="stylesheet" type="text/css" href="{$module_dir}css/addsharethis.css"/>
{if isset($addsharethis_data)}
<div id="ShareDiv" class="addsharethis">
<div class="addsharethisinner">
    <script type="text/javascript">var switchTo5x=true;</script>
    <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
    {literal}
    <script type="text/javascript">stLight.options({publisher: "{/literal}{$conf_row}{literal}", nativeCount:true });</script>
    {/literal}
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


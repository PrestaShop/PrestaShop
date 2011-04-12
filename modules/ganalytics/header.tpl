<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '{$ganalytics_id}']);
_gaq.push(['_trackPageview', '{$pageTrack}']);
{if $isOrder eq true}		{* If it's an order we need more data for stats *}
  _gaq.push(['_addTrans',
    '{$trans.id}',			{* order ID - required			*}
    '{$trans.store}',		{* affiliation or store name	*}
    '{$trans.total}',		{* total - required				*}
    '{$trans.tax}',			{* tax							*}
    '{$trans.shipping}',	{* shipping						*}
    '{$trans.city}',		{* city							*}
    '{$trans.state}',		{* state or province			*}
    '{$trans.country}'		{* country						*}
  ]);

	{foreach from=$items item=item}
		_gaq.push(['_addItem',
		'{$item.OrderId}',		{* order ID - required		*}
		'{$item.SKU}',			{* SKU/code - required		*}
		'{$item.Product}',		{* product name				*}
		'{$item.Category}',		{* category or variation	*}
		'{$item.Price}',		{* unit price - required	*}
		'{$item.Quantity}'		{* quantity - required		*}
		]);
	{/foreach}
	{* submits transaction to the Analytics servers *}
{literal}
  _gaq.push(['_trackTrans']);	
{/literal}
{/if}
{literal}
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})(); {/literal}
</script>
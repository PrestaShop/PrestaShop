{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if ($input.type == "description")}
		<p>{$input.text}</p>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="after"}
	<script type="text/javascript">
		startingData = new Array();
		{foreach from=$products item=product key=key}
			startingData[{$key}] = new Array(
				'{$product.details->name|@addcslashes:'\''}',
				'{$product.id_product|intval}',
				{$product.x_axis},
				{$product.y_axis},
				{$product.zone_width},
				{$product.zone_height});
		{/foreach}
	</script>
{/block}

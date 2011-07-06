<ul>
{foreach from=$virtualProducts item=product}
	<li>
		<a href="{$product.link}">{$product.name}</a>
		{if isset($product.deadline)}
			expire le {$product.deadline}
		{/if}
		{if isset($product.downloadable)}
			téléchargeable {$product.downloadable} fois
		{/if}
	</li>
{/foreach}
</ul>
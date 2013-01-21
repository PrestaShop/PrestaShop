{if $category->id == 1 OR $nb_products == 0}
	{l s='There are no products.'}
{else}
	{if $nb_products == 1}
		{l s='There is %d product.' sprintf=$nb_products}
	{else}
		{l s='There are %d products.' sprintf=$nb_products}
	{/if}
{/if}
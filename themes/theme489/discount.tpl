{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='My vouchers'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>{l s='My vouchers'}</h1>
{if isset($cart_rules) && count($cart_rules) && $nb_cart_rules}
<table class="discount std">
	<thead>
		<tr>
			<th class="discount_code first_item">{l s='Code'}</th>
			<th class="discount_description item">{l s='Description'}</th>
			<th class="discount_quantity item">{l s='Quantity'}</th>
			<th class="discount_value item">{l s='Value'}<sup>*</sup></th>
			<th class="discount_minimum item">{l s='Minimum'}</th>
			<th class="discount_cumulative item">{l s='Cumulative'}</th>
			<th class="discount_expiration_date last_item">{l s='Expiration date'}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$cart_rules item=discountDetail name=myLoop}
		<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
			<td class="discount_code">{$discountDetail.code}</td>
			<td class="discount_description">{$discountDetail.name}</td>
			<td class="discount_quantity">{$discountDetail.quantity_for_user}</td>
			<td class="discount_value">
            <span class="price">
				{if $discountDetail.id_discount_type == 1}
					{$discountDetail.value|escape:'htmlall':'UTF-8'}%
				{elseif $discountDetail.id_discount_type == 2}
					{convertPrice price=$discountDetail.value}
				{elseif $discountDetail.id_discount_type == 3}
					{l s='Free shipping'}
				{else}
					-
				{/if}
                </span>
			</td>
			<td class="discount_minimum">
				{if $discountDetail.minimal == 0}
					{l s='None'}
				{else}
					{convertPrice price=$discountDetail.minimal}
				{/if}
			</td>
			<td class="discount_cumulative">
				{if $discountDetail.cumulable == 1}
					<img src="{$img_dir}icon/yes.png" alt="{l s='Yes'}" class="icon" /> {l s='Yes'}
				{else}
					<img src="{$img_dir}icon/no.png" alt="{l s='No'}" class="icon" valign="middle" /> {l s='No'}
				{/if}
			</td>
			<td class="discount_expiration_date">{dateFormat date=$discountDetail.date_to}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
<p>
	<sup>*</sup>{l s='Tax included'}
</p>
{else}
	<p class="warning">{l s='You do not have any vouchers.'}</p>
{/if}
<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.png" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account.php', true)}">{l s='Back to Your Account'}</a></li>
	<li><a href="{$base_dir}"><img src="{$img_dir}icon/home.png" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home'}</a></li>
</ul>
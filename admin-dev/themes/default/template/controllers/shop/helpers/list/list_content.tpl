{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/list/list_content.tpl"}

{block name="open_td"}
	{if $key == 'url'}
		<td{if isset($params.position)} id="td_{if !empty($position_group_identifier)}{$position_group_identifier}{else}0{/if}_{$tr.$identifier}{if $smarty.capture.tr_count > 1}_{($smarty.capture.tr_count - 1)|intval}{/if}"{/if} class="{if !$no_link}pointer{/if}{if isset($params.class)} {$params.class}{/if}{if isset($params.align)} {$params.align}{/if}">
	{else}
		<td class="pointer" onclick="document.location = '{$current_index|addslashes|escape:'html':'UTF-8'}&amp;shop_id={$tr.$identifier|escape:'html':'UTF-8'}{if $view}&amp;view{else}&amp;update{/if}{$table|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}'">
	{/if}
{/block}

{block name="td_content"}
	{if $key == 'url'}
		{if isset($tr.$key)}
			<a href="{$tr.$key}" onmouseover="$(this).css('text-decoration', 'underline')" onmouseout="$(this).css('text-decoration', 'none')" target="_blank" rel="noopener noreferrer nofollow">{$tr.$key}</a>
		{else}
			<a href="{$link->getAdminLink('AdminShopUrl', true, [], ['addshop_url' => 1, 'shop_id' => $tr.$identifier|intval])|escape:'html':'UTF-8'}" class="multishop_warning">{l s='Click here to set a URL for this shop.' d='Admin.Shopparameters.Notification'}</a>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/list/list_content.tpl"}
			{block name="open_td"}
				<td
					{if isset($params.position)}
						id="td_{if !empty($position_group_identifier)}{$position_group_identifier}{else}0{/if}_{$tr.$identifier}"
					{/if}
					class="{strip}{if !$no_link}pointer{/if}
					{if isset($params.position) && $order_by == 'position'  && $order_way != 'DESC'} dragHandle{/if}
					{if isset($params.class)} {$params.class}{/if}
					{if isset($params.align)} {$params.align}{/if}{/strip}"
					{if (!isset($params.position) && !$no_link && !isset($params.remove_onclick))}
						onclick="document.location = '{$current_index|addslashes|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$tr.$identifier|escape:'html':'UTF-8'}&amp;details{$table}&amp;token={$token|escape:'html':'UTF-8'}'">
					{else}
					>
					{/if}
			{/block}

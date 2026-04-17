{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/list/list_content.tpl"}
			{block name="open_td"}
				<td
					{if isset($params.position)}
						id="td_{if !empty($id_category)}{$id_category}{else}0{/if}_{$tr.$identifier}"
					{/if}
					class="{if !$no_link}pointer{/if}
					{if isset($params.position) && $order_by == 'position'  && $order_way != 'DESC'} dragHandle{/if}
					{if isset($params.align)} {$params.align}{/if}"
					{if (!isset($params.position) && !$no_link && !isset($params.remove_onclick))}
            {assign var="identifier_field" value=$identifier|escape:'html':'UTF-8'}
						onclick="document.location = '{$link->getAdminLink('AdminCarrierWizard', true, [], [$identifier_field => $tr.$identifier|escape:'html':'UTF-8'])|escape:'html':'UTF-8'}'">
					{else}
						>
					{/if}
			{/block}

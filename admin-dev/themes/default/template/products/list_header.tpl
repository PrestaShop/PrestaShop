{*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helper/list/list_header.tpl"}

{block name=leadin}
	{if isset($category_tree)}
		<script type="text/javascript">
			$(document).ready(function(){
				$('#go_to_categ').bind('change', function(){
					var base_url = '{$base_url}';
					if (this.value !== "")
						location.href = base_url + '&id_category=' + parseInt(this.value);
					else
						location.href = base_url;
				});
			});
		</script>
		{l s='Go to category:'}
		<select id="go_to_categ" name="go_to_categ">
		{foreach from=$category_tree item=categ}
			<option value="{$categ->id}" {if $categ->selected}selected="selected"{/if} >
				{$categ->dashes}{$categ->name}
			</option>
		{/foreach}
		</select>
	{/if}
{/block}

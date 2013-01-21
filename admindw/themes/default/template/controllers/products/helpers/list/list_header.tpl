{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14989 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
	{if isset($category_tree)}
		<script type="text/javascript">
			$(document).ready(function(){
				var base_url = '{$base_url}';
				// Load category products page when category is clicked
				$('#categories-treeview :input').live('click', function(){
					if (this.value !== "")
						location.href = base_url + '&id_category=' + parseInt(this.value);
					else
						location.href = base_url;
				});

				// Make sure the checkbox is checked/unchecked when the link is clicked
				$('#toggle_category_tree').bind('click', function(event){
					event.preventDefault();
					$('#block_category_tree').toggle();
					if ($('#block_category_tree').is(':visible'))
						$(this).find('input').attr('checked', true);
					else
					{
						$(this).find('input').removeAttr('checked');
						location.href = base_url;
					}
				});
			});

		</script>
		<div class="bloc-leadin">
			<div id="container_category_tree">
				<a href="#" id="toggle_category_tree">
					<form>
						<input type="checkbox" {if $is_category_filter}checked="checked"{/if} />{l s='Filter by category'}
					</form>
				</a>
				<div id="block_category_tree" {if !$is_category_filter}style="display:none"{/if}>
					<br />
					{$category_tree}
				</div>
			</div>
		</div>
	{/if}
{/block}

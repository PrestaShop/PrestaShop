{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="other_input"}
	{if $key eq 'selects'}
	<div class="margin-form">
	<h3>{l s='Products:'}</h3>
	<table class="double_select">
		<tr>
			<td style="padding-left:20px;">
				<select multiple id="select_left">
					{foreach from=$field.products_unselected item='product'}
					<option value="{$product.id_product}">{$product.name}</option>
					{/foreach}
				</select>
				<span class="hint" name="help_box">{l s='Double click to move the item to another column'}<span class="hint-pointer">&nbsp;</span></span>
				<br /><br />
				<a href="#" id="move_to_right" class="multiple_select_add">
					{l s='Add'} &gt;&gt;
				</a>
			</div>
			</td>
			<td>
				<select multiple id="select_right" name="products[]">
					{foreach from=$field.products item='product'}
					<option selected="selected" value="{$product.id_product}">{$product.name}</option>
					{/foreach}
				</select>
				<span class="hint" name="help_box">{l s='Double click to move the item to another column'}<span class="hint-pointer">&nbsp;</span></span>
				<br /><br />
				<a href="#" id="move_to_left" class="multiple_select_remove">
					&lt;&lt; {l s='Remove'}
				</a>
			</td>
		</tr>
	</table>
	</div>
	<div class="clear">&nbsp;</div>

	<script type="text/javascript">
	$(document).ready(function(){
		$('#move_to_right').click(function(){
			return !$('#select_left option:selected').remove().appendTo('#select_right');
		})
		$('#move_to_left').click(function(){
			return !$('#select_right option:selected').remove().appendTo('#select_left');
		});
		$('#select_left option').live('dblclick', function(){
			$(this).remove().appendTo('#select_right');
		});
		$('#select_right option').live('dblclick', function(){
			$(this).remove().appendTo('#select_left');
		});
	});
	$('#tag_form').submit(function()
	{
		$('#select_right option').each(function(i){
			$(this).attr("selected", "selected");
		});
	});
	</script>
	{/if}
{/block}

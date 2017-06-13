{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file="helpers/form/form.tpl"}

{block name="other_input"}
{if $key eq 'selects'}
<div class="row">
	<label class="control-label col-lg-3">{l s='Products' d='Admin.Global'}</label>

	<div class="col-lg-9">
		<div class="row">
			<div class="col-lg-6">
				<select multiple id="select_left">
					{foreach from=$field.products_unselected item='product'}
					<option value="{$product.id_product}">{$product.name}</option>
					{/foreach}
				</select>
				<a href="#" id="move_to_right" class="btn btn-default btn-block multiple_select_add">
					{l s='Add' d='Admin.Actions'} <i class="icon-arrow-right"></i>
				</a>
			</div>
			<div class="col-lg-6">
				<select multiple id="select_right" name="products[]">
					{foreach from=$field.products item='product'}
					<option selected="selected" value="{$product.id_product}">{$product.name}</option>
					{/foreach}
				</select>
				<a href="#" id="move_to_left" class="btn btn-default btn-block multiple_select_remove">
					<i class="icon-arrow-left"></i> {l s='Remove' d='Admin.Actions'}
				</a>
			</div>
		</div>
	</div>
</div>


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

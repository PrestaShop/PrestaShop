{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
					<option value="{$product.id_product}">{$product.name|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
				<a href="#" id="move_to_right" class="btn btn-default btn-block multiple_select_add">
					{l s='Add' d='Admin.Actions'} <i class="icon-arrow-right"></i>
				</a>
			</div>
			<div class="col-lg-6">
				<select multiple id="select_right" name="products[]">
					{foreach from=$field.products item='product'}
					<option selected="selected" value="{$product.id_product}">{$product.name|escape:'html':'UTF-8'}</option>
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
	$(function(){
		$('#move_to_right').on('click', function(){
			return !$('#select_left option:selected').remove().appendTo('#select_right');
		})
		$('#move_to_left').on('click', function(){
			return !$('#select_right option:selected').remove().appendTo('#select_left');
		});
		$(document).on('dblclick', '#select_left option', function(e) {
			$(this).remove().appendTo('#select_right');
		});
		$(document).on('dblclick', '#select_right option', function(e) {
			$(this).remove().appendTo('#select_left');
		});
	});
	$('#tag_form').on('submit', function()
	{
		$('#select_right option').each(function(i){
			$(this).prop('selected', 'selected');
		});
	});
	</script>
	{/if}
{/block}

{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}

{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input['type'] == 'modules'}
		<div {if !$form_id}class="hide"{/if}>
			<label class="control-label col-lg-3">{l s='Authorized modules:' d='Admin.Shopparameters.Feature'}</label>
		</div>
	{elseif $input['type'] == 'group_discount_category'}
		<div {if !$form_id}class="hide"{/if}>
			{$smarty.block.parent}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="field"}
	{if $input['type'] == 'group_discount_category'}
	<div {if !$form_id}class="hide"{/if}>
		<script type="text/javascript">
		$(function() {
			$("#group_discount_category").fancybox({
				beforeLoad: function () {
					$('#group_discount_category_fancybox').show();
					$('#group_discount_category_fancybox .tree-panel-heading-controls').css('margin', '-40px 0px 20px');
					$('#group_discount_category_fancybox .form-horizontal').css('padding', '10px');
					initFancyBox();
				},
				beforeClose: function () {
					$('#group_discount_category_fancybox').hide();
				}
			});
		});

		function deleteCategoryReduction(id_category)
		{
			$('#group_discount_category_table tr#'+id_category).fadeOut('slow', function () {
				$(this).remove();
			});

		}

		function addCategoryReduction()
		{
			exist = false;
			$('.category_reduction').each( function () {
				if ($(this).attr('name') == 'category_reduction['+$('[name="id_category"]:checked').val()+']')
				{
					exist = true;
					jAlert('{l s='This category already exists for this group.' js=1 d='Admin.Shopparameters.Feature'}');
					return false;
				}
			});

			if (exist)
				return;
			$.ajax({
				type:"POST",
				url: "index.php",
				async: true,
				dataType: "json",
				data : {
					ajax: "1",
					token: "{getAdminToken tab='AdminGroups'}",
					controller: "AdminGroups",
					action: "addCategoryReduction",
					category_reduction: $('#category_reduction_fancybox').val() ,
					id_category: $('[name="id_category"]:checked').val()
				},
				success : function(jsonData) {
					if (jsonData.hasError)
					{
						var errors = '';
						for (error in jsonData.errors)
							//IE6 bug fix
							if (error != 'indexOf')
								errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
						jAlert(errors);
					}
					else
					{
						$('#group_discount_category_table').append('<tr class="alt_row" id="'+jsonData.id_category+'"><td>'+jsonData.catPath+'</td><td>{l s='Discount' d='Admin.Global'}' + ' ' + jsonData.discount+'{l s='%'}</td><td><a href="#" onclick="deleteCategoryReduction('+jsonData.id_category+');" class="btn btn-default"><i class="icon-trash"></i> {l s='Delete' d='Admin.Actions'}</a></td></tr>');
						var input_hidden = document.createElement("input");
						input_hidden.setAttribute('type', 'hidden');
						input_hidden.setAttribute('value', jsonData.discount);
						input_hidden.setAttribute('name', 'category_reduction['+jsonData.id_category+']');
						input_hidden.setAttribute('class', 'category_reduction');

						$('#group_discount_category_table tr#'+jsonData.id_category+' > td:last').append(input_hidden);
						$.fancybox.close();
					}
				}
			});

			return false;
		}

		function initFancyBox()
		{
			$('[name="id_category"]:checked').removeAttr('checked');
			$('#category_reduction_fancybox').val('0.00');
		}
		</script>

		<div class="col-lg-8">
			<a class="btn btn-default" href="#group_discount_category_fancybox" id="group_discount_category">{l s='Add a category discount' d='Admin.Shopparameters.Feature'}</a>
			<table class="table" id="group_discount_category_table">
				{foreach $input['values'] key=key item=category }
					<tr class="alt_row" id="{$category.id_category}">
						<td>{$category.path}</td>
						<td>{l s='Discount: %.2f%%' sprintf=[$category.reduction] d='Admin.Shopparameters.Feature'}</td>
						<td>
							<a href="#" onclick="deleteCategoryReduction({$category.id_category});"class="btn btn-default">
								<i class="icon-trash"></i> {l s='Delete' d='Admin.Actions'}
							</a>
							<input type="hidden" class="category_reduction" name="category_reduction[{$category.id_category}]" value="{$category.reduction}">
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
		<div id="group_discount_category_fancybox" class="bootstrap" style="display:none" >
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-group"></i> {l s='New group category discount' d='Admin.Shopparameters.Feature'}
				</div>
				{$categoryTreeView}
				<div class="form-horizontal">
				<div class="alert alert-info">{l s='Caution: The discount applied to a category does not stack with the overall reduction but instead replaces it.' d='Admin.Shopparameters.Notification'}</div>
				<div class="alert alert-warning">{l s='Only products that have this category as the default category will be affected.' d='Admin.Shopparameters.Notification'}</div>
				<div class="form-group">
					<label class="control-label col-lg-3" for="category_reduction_fancybox">{l s='Discount (%):' d='Admin.Shopparameters.Feature'}</label>
					<div class="col-lg-9">
						<input type="text" name="category_reduction_fancybox" id="category_reduction_fancybox" value="0.00" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12">
						<button type="button" onclick="addCategoryReduction();" class="btn btn-default pull-right">{l s='Add' d='Admin.Actions'}</button>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
	{elseif $input['type'] == 'modules'}
	<div {if !$form_id}class="hide"{/if}>
		<script type="text/javascript">
			$(function() {
				$('#authorized-modules').find('[value="0"]').on('click', function() {
					$(this).parent().parent().find('input[type=hidden]').attr('name', 'modulesBoxUnauth[]');
				});

				$('#authorized-modules').find('[value="1"]').on('click', function() {
					$(this).parent().parent().find('input[type=hidden]').attr('name', 'modulesBoxAuth[]');
				});
			});
		</script>

		<div class="col-lg-9" id="authorized-modules">
			{foreach $input['values']['auth_modules'] key=key item=module }
			<div class="form-group">
				<label class="control-label col-lg-4"><img src="../modules/{$module->name|escape:'html':'UTF-8'}/logo.png" height="16" width="16" alt="{$module->displayName|escape:'html':'UTF-8'}" /> {$module->displayName|escape:'html':'UTF-8'}</label>
				<div class="input-group col-lg-8">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="{$module->name|escape:'html':'UTF-8'}" id="{$module->name|escape:'html':'UTF-8'}_on" value="1" checked="checked"/>
						<label for="{$module->name|escape:'html':'UTF-8'}_on">{l s='Yes' d='Admin.Global'}</label>
						<input type="radio" name="{$module->name|escape:'html':'UTF-8'}" id="{$module->name|escape:'html':'UTF-8'}_off" value="0"/>
						<label for="{$module->name|escape:'html':'UTF-8'}_off">{l s='No' d='Admin.Global'}</label>
						<a class="slide-button btn"></a>
					</span>
					<input type="hidden" name="modulesBoxAuth[]" value="{$module->id|intval}"/>
				</div>
			</div>
			{/foreach}
			{foreach $input['values']['unauth_modules'] key=key item=module }
			<div class="form-group">
				<label class="control-label col-lg-4"><img src="../modules/{$module->name|escape:'html':'UTF-8'}/logo.png" height="16" width="16" alt="{$module->displayName|escape:'html':'UTF-8'}"/> {$module->displayName|escape:'html':'UTF-8'}</label>
				<div class="input-group col-lg-8">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="{$module->name|escape:'html':'UTF-8'}" id="{$module->name|escape:'html':'UTF-8'}_on" value="1"/>
						<label for="{$module->name|escape:'html':'UTF-8'}_on">{l s='Yes' d='Admin.Global'}</label>
						<input type="radio" name="{$module->name|escape:'html':'UTF-8'}" id="{$module->name|escape:'html':'UTF-8'}_off" value="0" checked="checked"/>
						<label for="{$module->name|escape:'html':'UTF-8'}_off">{l s='No' d='Admin.Global'}</label>
						<a class="slide-button btn"></a>
					</span>
					<input type="hidden" name="modulesBoxUnauth[]" value="{$module->id|intval}"/>
				</div>
			</div>
			{/foreach}
		</div>
	</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="other_fieldsets"}
<div id="conditions">
	<div id="condition_group_list"></div>
</div>

<a class="btn btn-default" href="#" id="add_condition_group">
	<i class="icon-plus-sign"></i> {l s='Add a new condition group'}
</a>
<div class="clearfix">&nbsp;</div>
<div class="panel" id="conditions-panel" style="display:none;">
	<h3><i class="icon-tasks"></i> {l s='Conditions'}</h3>
	<div class="form-group condition-category">
		<label for="id_category" class="control-label col-lg-3">{l s='Category'}</label>
		<div class="col-lg-9">
			<div class="col-lg-8">
				<select id="id_category" name="id_category">
					{foreach from=$categories item='category'}
					<option value="{$category.id_category|intval}">({$category.id_category|intval}) {$category.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default add_condition" href="#">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group condition-manufacturer">
		<label for="id_manufacturer" class="control-label col-lg-3">{l s='Manufacturer'}</label>
		<div class="col-lg-9">
			<div class="col-lg-8">
				<select id="id_manufacturer" name="id_manufacturer">
					{foreach from=$manufacturers item='manufacturer'}
						<option value="{$manufacturer.id_manufacturer}">{$manufacturer.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default add_condition" href="#">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group condition-supplier">
		<label for="id_supplier" class="control-label col-lg-3">{l s='Supplier'}</label>
		<div class="col-lg-9">
			<div class="col-lg-8">
				<select id="id_supplier" name="id_supplier">
					{foreach from=$suppliers item='supplier'}
						<option value="{$supplier.id_supplier}">{$supplier.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default add_condition" href="#">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group condition-attribute">
		<label for="id_attribute_group" class="control-label col-lg-3">{l s='Attributes'}</label>
		<div class="col-lg-9">
			<div class="col-lg-4">
				<select id="id_attribute_group">
					{foreach from=$attributes_group item='attribute_group'}
						<option value="{$attribute_group.id_attribute_group}">{$attribute_group.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-4">
				{foreach from=$attributes_group item='attribute_group'}
					<select class="id_attribute" style="display:none;" id="id_attribute_{$attribute_group.id_attribute_group}">
						{foreach from=$attribute_group.attributes item='attribute'}
							<option value="{$attribute.id_attribute}" data-attribute-group-id="{$attribute_group.id_attribute_group}">{$attribute.name}</option>
						{/foreach}
					</select>
				{/foreach}
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default add_condition" href="#">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group condition-feature">
		<label for="id_feature" class="control-label col-lg-3">{l s='Features'}</label>
		<div class="col-lg-9">
			<div class="col-lg-4">
				<select id="id_feature">
					{foreach from=$features item='feature'}
						<option value="{$feature.id_feature}">{$feature.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-4">
				{foreach from=$features item='feature'}
					<select class="id_feature_value" style="display:none;" id="id_feature_{$feature.id_feature}">
						{foreach from=$feature.values item='value'}
							<option value="{$value.id_feature_value}" data-feature-id="{$feature.id_feature}">{$value.value}</option>
						{/foreach}
					</select>
				{/foreach}
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default add_condition" href="#">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
{if !$is_multishop}
	<input type="hidden" name="id_shop" value="1" />
{/if}
</div>
{/block}

{block name="script"}
var current_id_condition_group = 0;
var last_condition_group = 0;
var conditions = new Array();
var conditionBuilder = {
	'category': {
		'value': function() { return $('#id_category option:selected').val(); },
		'html': function (id_condition, value) {
			return '<tr id="'+id_condition+'"><td>{l s='Category'}</td><td>'+$('#id_category option[value='+value+']').html()+'</td><td><a href="#" class="btn btn-default btn-delete-condition"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		},
	},
	'manufacturer': {
		'value': function () { return $('#id_manufacturer option:selected').val(); },
		'html': function (id_condition, value) {
			return '<tr id="'+id_condition+'"><td>{l s='Manufacturer'}</td><td>'+$('#id_manufacturer option[value='+value+']').html()+'</td><td><a href="#" class="btn btn-default btn-delete-condition"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		},
	},
	'supplier': {
		'value': function () { return $('#id_supplier option:selected').val(); },
		'html': function (id_condition, value) {
			return '<tr id="'+id_condition+'"><td>{l s='Supplier'}</td><td>'+$('#id_supplier option[value='+value+']').html()+'</td><td><a href="#" class="btn btn-default btn-delete-condition"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		},
	},
	'attribute': {
		'value': function () { return $('#id_attribute_'+$('#id_attribute_group option:selected').val()+' option:selected').val(); },
		'html': function (id_condition, value) {
			var attribute = $('.id_attribute option[value='+value+']');
			var attribute_group_id = attribute.data('attribute-group-id');
			var attribute_name = $('#id_attribute_group option[value='+attribute_group_id+']').html();
			var attribute_value = attribute.html();
			return '<tr id="'+id_condition+'"><td>{l s='Attribute'}</td><td>'+attribute_name+': '+attribute_value+'</td><td><a href="#" class="btn btn-default btn-delete-condition"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		},
	},
	'feature': {
		'value': function () { return $('#id_feature_'+$('#id_feature option:selected').val()+' option:selected').val(); },
		'html': function (id_condition, value) {
			var feature = $('.id_feature_value option[value='+value+']');
			var feature_id = feature.data('feature-id');
			var feature_name = $('#id_feature option[value='+feature_id+']').html();
			var feature_value = feature.html();
			return '<tr id="'+id_condition+'"><td>{l s='Feature'}</td><td>'+feature_name+': '+feature_value+'</td><td><a href="#" class="btn btn-default btn-delete-condition"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		},
	},
};

function toggle_condition_group(id_condition_group)
{
	$('.condition_group').removeClass('alert-info');
	$('.condition_group > table').removeClass('alert-info');
	$('#condition_group_'+id_condition_group+' > table').addClass('alert-info');
	$('#condition_group_'+id_condition_group).addClass('alert-info');
	current_id_condition_group = id_condition_group;
}

function add_condition(id_condition_group, type, value)
{
	var id_condition = id_condition_group+'_'+type+'_'+value;
	if (typeof conditions[id_condition] != 'undefined')
		return false;
	var condition = new Array();
	condition.type = type;
	condition.value = value;
	condition.id_condition_group = id_condition_group;
	conditions[id_condition] = condition;
	return id_condition;
}

function delete_condition(condition)
{
	delete conditions[condition];

	to_delete = $('#'+condition).prev();
	if ($(to_delete).children().hasClass('condition-group-separator'))
		$(to_delete).remove();
	else
		$('#'+condition).next().remove();

	$('#'+condition).remove();
	return false;
}

function new_condition_group()
{
	$('#conditions-panel').show();
	var html = '';

	if (last_condition_group > 0)
		html += '<div class="row condition_separator text-center">{l s='OR'}</div><div class="clearfix">&nbsp;</div>';

	last_condition_group++;
	html += '<div id="condition_group_'+last_condition_group+'" class="panel condition_group alert-info"><h3><i class="icon-tasks"></i> {l s='Condition group'} '+last_condition_group+'</h3>';
		html += '<table class="table alert-info"><thead><tr><th class="fixed-width-md"><span class="title_box">{l s='Type'}</span></th><th><span class="title_box">{l s='Value'}</span></th><th></th></tr></thead><tbody></tbody></table>';
		html += '</div>';
	$('#condition_group_list').append(html);
	toggle_condition_group(last_condition_group);
}

function appendConditionToGroup(id_condition, html)
{
	var condition = conditions[id_condition];
	if (condition == undefined) return;

	if ($('#condition_group_'+condition.id_condition_group+' table tbody tr').length > 0)
		$('#condition_group_'+condition.id_condition_group+' table tbody').append('<tr><td class="text-center condition-group-separator" colspan="3"><b>{l s='AND' js=1}</b></td></tr>');
	$('#condition_group_'+condition.id_condition_group+' table tbody').append(html);
	$('#conditions #'+id_condition+' a.btn-delete-condition').click(function() {
		delete_condition(id_condition);
		return false;
	});
}

function createCondition(condition_type, value) {
	var builder = conditionBuilder[condition_type];
	if (builder == undefined) return;
	if (value == undefined) value = builder.value();

	var id_condition = add_condition(current_id_condition_group, condition_type, value);
	if (!id_condition) return;

	appendConditionToGroup(id_condition, builder.html(id_condition, value));
}

$(document).ready(function() {
	$('#leave_bprice_on').click(function() {
		if (this.checked)
			$('#price').attr('disabled', 'disabled');
		else
			$('#price').removeAttr('disabled');
	});

	$('#specific_price_rule_form').live('submit', function(e) {
		var html = '';
		for (i in conditions)
			html += '<input type="hidden" name="condition_group_'+conditions[i].id_condition_group+'[]" value="'+conditions[i].type+'_'+conditions[i].value+'" />';
		$('#conditions').append(html);
	});

	$('#id_feature').change(function() {
		$('.id_feature_value').hide();
		$('#id_feature_'+$(this).val()).show();
	});

	$('#id_attribute_group').change(function() {
		$('.id_attribute').hide();
		$('#id_attribute_'+$(this).val()).show();
	});

	$('#conditions-panel .add_condition').click(function() {
		var matches = $(this).closest('.form-group').attr('class').match(/condition-(.*)/);
		if (matches == null) return false;
		createCondition(matches[1]);
		return false;
	});

	$('#add_condition_group').click(function() {
		new_condition_group();
		return false;
	});

	$('.condition_group').live('click', function() {
		var id = this.id.split('_');
		toggle_condition_group(id[2]);
		return false;
	});

	{foreach from=$conditions key='id_group_condition' item='condition_group'}
		new_condition_group();
		{foreach from=$condition_group item='condition'}
			createCondition('{$condition.type}', {$condition.value});
		{/foreach}
	{/foreach}
	$('#id_attribute_group').change();
	$('#id_feature').change();
});
{/block}

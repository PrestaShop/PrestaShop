/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function addProductRuleGroup()
{
	product_rule_groups_counter += 1;
	product_rule_counters[product_rule_groups_counter] = 0;

	$.get(
		'ajax-tab.php',
		{controller:'AdminCartRules',token:currentToken,newProductRuleGroup:1,product_rule_group_id:product_rule_groups_counter},
		function(content) {
			if (content != "")
				$('#product_rule_group_table').append(content);
		}
	);
}

function removeProductRuleGroup(id)
{
	$('#product_rule_group_' + id + '_tr').remove();
}

function addProductRule(product_rule_group_id)
{
	product_rule_counters[product_rule_group_id] += 1;
	if ($('#product_rule_type_' + product_rule_group_id).val() != 0)
		$.get(
			'ajax-tab.php',
			{controller:'AdminCartRules',token:currentToken,newProductRule:1,product_rule_type:$('#product_rule_type_' + product_rule_group_id).val(),product_rule_group_id:product_rule_group_id,product_rule_id:product_rule_counters[product_rule_group_id]},
			function(content) {
				if (content != "")
					$('#product_rule_table_' + product_rule_group_id).append(content);
			}
		);
}

function removeProductRule(product_rule_group_id, product_rule_id)
{
	$('#product_rule_' + product_rule_group_id + '_' + product_rule_id + '_tr').remove();
}

function toggleCartRuleFilter(id)
{
	if ($(id).prop('checked'))
		$('#' + $(id).attr('id') + '_div').show(400);
	else
		$('#' + $(id).attr('id') + '_div').hide(200);
}

function removeCartRuleOption(item)
{
	var id = $(item).attr('id').replace('_remove', '');
	$('#' + id + '_2 option:selected').remove().appendTo('#' + id + '_1');
}

function addCartRuleOption(item)
{
	var id = $(item).attr('id').replace('_add', '');
	$('#' + id + '_1 option:selected').remove().appendTo('#' + id + '_2');
}

function updateProductRuleShortDescription(item)
{
	var id1 = $(item).attr('id').replace('_add', '').replace('_remove', '');
	var id2 = id1.replace('_select', '');
	var length = $('#' + id1 + '_2 option').length;
	if (length == 1)
		$('#' + id2 + '_match').val($('#' + id1 + '_2 option').first().text().trim());
	else
		$('#' + id2 + '_match').val(length);
}

var restrictions = new Array('country', 'carrier', 'group', 'cart_rule', 'shop');
for (i in restrictions)
{
	toggleCartRuleFilter($('#' + restrictions[i] + '_restriction'));
	$('#' + restrictions[i] + '_restriction').click(function() {toggleCartRuleFilter(this);});
	$('#' + restrictions[i] + '_select_remove').click(function() {removeCartRuleOption(this);});
	$('#' + restrictions[i] + '_select_add').click(function() {addCartRuleOption(this);});
}
toggleCartRuleFilter($('#product_restriction'));
$('#product_restriction').click(function() {toggleCartRuleFilter(this);});

function toggleApplyDiscount(percent, amount, apply_to)
{
	if (percent)
	{
		$('#apply_discount_percent_div').show(400);
		if ($('#apply_discount_to_product').prop('checked'))
			toggleApplyDiscountTo();
		$('#apply_discount_to_cheapest').show();
		$('*[for=apply_discount_to_cheapest]').show();
		$('#apply_discount_to_selection').show();
		$('*[for=apply_discount_to_selection]').show();
	}
	else
	{
		$('#apply_discount_percent_div').hide(200);
		$('#reduction_percent').val('0');
	}
	
	if (amount)
	{
		$('#apply_discount_amount_div').show(400);
		if ($('#apply_discount_to_product').prop('checked'))
			toggleApplyDiscountTo();
		$('#apply_discount_to_cheapest').hide();
		$('*[for=apply_discount_to_cheapest]').hide();
		$('#apply_discount_to_cheapest').removeAttr('checked');
		$('#apply_discount_to_selection').hide();
		$('*[for=apply_discount_to_selection]').hide();
		$('#apply_discount_to_selection').removeAttr('checked');
	}
	else
	{
		$('#apply_discount_amount_div').hide(200);
		$('#reduction_amount').val('0');
	}
		
	if (apply_to)
		$('#apply_discount_to_div').show(400);
	else
	{
		toggleApplyDiscountTo();
		$('#apply_discount_to_div').hide(200);
	}
}

function toggleApplyDiscountTo()
{
	if ($('#apply_discount_to_product').prop('checked'))
		$('#apply_discount_to_product_div').show(400);
	else
	{
		$('#apply_discount_to_product_div').hide(200);
		$('#reductionProductFilter').val('');
		if ($('#apply_discount_to_order').prop('checked'))
			$('#reduction_product').val('0');
		if ($('#apply_discount_to_cheapest').prop('checked'))
			$('#reduction_product').val('-1');
		if ($('#apply_discount_to_selection').prop('checked'))
			$('#reduction_product').val('-2');
	}
}

function toggleGiftProduct()
{
	if ($('#free_gift_on').prop('checked'))
		$('#free_gift_div').show(400);
	else
	{
		$('#gift_product').val('0');
		$('#giftProductFilter').val('');
		$('#free_gift_div').hide(200);
	}
}

$('#apply_discount_percent').click(function() {toggleApplyDiscount(true, false, true);});
if ($('#apply_discount_percent').prop('checked'))
	toggleApplyDiscount(true, false, true);

$('#apply_discount_amount').click(function() {toggleApplyDiscount(false, true, true);});
if ($('#apply_discount_amount').prop('checked'))
	toggleApplyDiscount(false, true, true);

$('#apply_discount_off').click(function() {toggleApplyDiscount(false, false, false);});
if ($('#apply_discount_off').prop('checked'))
	toggleApplyDiscount(false, false, false);

$('#apply_discount_to_order').click(function() {toggleApplyDiscountTo();});
if ($('#apply_discount_to_order').prop('checked'))
	toggleApplyDiscountTo();
	
$('#apply_discount_to_product').click(function() {toggleApplyDiscountTo();});
if ($('#apply_discount_to_product').prop('checked'))
	toggleApplyDiscountTo();
	
$('#apply_discount_to_cheapest').click(function() {toggleApplyDiscountTo();});
if ($('#apply_discount_to_cheapest').prop('checked'))
	toggleApplyDiscountTo();
	
$('#apply_discount_to_selection').click(function() {toggleApplyDiscountTo();});
if ($('#apply_discount_to_selection').prop('checked'))
	toggleApplyDiscountTo();
	
$('#free_gift_on').click(function() {toggleGiftProduct();});
$('#free_gift_off').click(function() {toggleGiftProduct();});
toggleGiftProduct();

// Main form submit
$('#cart_rule_form').submit(function() {
	if ($('#customerFilter').val() == '')
		$('#id_customer').val('0');

	for (i in restrictions)
	{
		if ($('#' + restrictions[i] + '_select_1 option').length == 0)
			$('#' + restrictions[i] + '_restriction').removeAttr('checked');
		else
		{
			$('#' + restrictions[i] + '_select_2 option').each(function(i) {
				$(this).attr('selected', true);
			});
		}
	}
	
	$('.product_rule_toselect option').each(function(i) {
		$(this).attr('selected', true);
	});
});
	
$('#reductionProductFilter')
	.autocomplete(
			'ajax-tab.php', {
			minChars: 2,
			max: 50,
			width: 500,
			selectFirst: false,
			scroll: false,
			dataType: 'json',
			formatItem: function(data, i, max, value, term) {
				return value;
			},
			parse: function(data) {
				var mytab = new Array();
				for (var i = 0; i < data.length; i++)
					mytab[mytab.length] = { data: data[i], value: (data[i].reference + ' ' + data[i].name).trim() };
				return mytab;
			},
			extraParams: {
				controller: 'AdminCartRules',
				token: currentToken,
				reductionProductFilter: 1
			}
		}
	)
	.result(function(event, data, formatted) {
		$('#reduction_product').val(data.id_product);
		$('#reductionProductFilter').val((data.reference + ' ' + data.name).trim());
	});
	
$('#customerFilter')
	.autocomplete(
			'ajax-tab.php', {
			minChars: 2,
			max: 50,
			width: 500,
			selectFirst: false,
			scroll: false,
			dataType: 'json',
			formatItem: function(data, i, max, value, term) {
				return value;
			},
			parse: function(data) {
				var mytab = new Array();
				for (var i = 0; i < data.length; i++)
					mytab[mytab.length] = { data: data[i], value: data[i].cname + ' (' + data[i].email + ')' };
				return mytab;
			},
			extraParams: {
				controller: 'AdminCartRules',
				token: currentToken,
				customerFilter: 1
			}
		}
	)
	.result(function(event, data, formatted) {
		$('#id_customer').val(data.id_customer);
		$('#customerFilter').val(data.cname + ' (' + data.email + ')');
	});

function displayCartRuleTab(tab)
{
	$('.cart_rule_tab').hide();
	$('.tab-page').removeClass('selected');
	$('#cart_rule_' + tab).show();
	$('#cart_rule_link_' + tab).addClass('selected');
	$('#currentFormTab').val(tab);
}

$('.cart_rule_tab').hide();
$('.tab-page').removeClass('selected');
$('#cart_rule_' + currentFormTab).show();
$('#cart_rule_link_' + currentFormTab).addClass('selected');

var date = new Date();
var hours = date.getHours();
if (hours < 10)
	hours = "0" + hours;
var mins = date.getMinutes();
if (mins < 10)
	mins = "0" + mins;
var secs = date.getSeconds();
if (secs < 10)
	secs = "0" + secs;
$('.datepicker').datepicker({
	prevText: '',
	nextText: '',
	dateFormat: 'yy-mm-dd ' + hours + ':' + mins + ':' + secs
});

$('#giftProductFilter').typeWatch({
	captureLength: 2,
	highlight: false,
	wait: 100,
	callback: function(){ searchProducts(); }
});

var gift_product_search = $('#giftProductFilter').val();
function searchProducts()
{
	if ($('#giftProductFilter').val() == gift_product_search)
		return;
	gift_product_search = $('#giftProductFilter').val();
	
	$.ajax({
		type: 'POST',
		url: 'ajax-tab.php',
		async: true,
		dataType: 'json',
		data: {
			controller: 'AdminCartRules',
			token: currentToken,
			action: 'searchProducts',
			product_search: $('#giftProductFilter').val()
		},
		success : function(res)
		{
			var products_found = '';
			var attributes_html = '';
			stock = {};
			
			if (res.found)
			{
				$('#gift_products_err').hide();
				$('#gift_products_found').show();
				$.each(res.products, function() {
					products_found += '<option value="' + this.id_product + '">' + this.name + (this.combinations.length == 0 ? ' - ' + this.formatted_price : '') + '</option>';
					
					attributes_html += '<select class="id_product_attribute" id="ipa_' + this.id_product + '" name="ipa_' + this.id_product + '" style="display:none">';
					$.each(this.combinations, function() {
						attributes_html += '<option ' + (this.default_on == 1 ? 'selected="selected"' : '') + ' value="' + this.id_product_attribute + '">' + this.attributes + ' - ' + this.formatted_price + '</option>';
					});
					attributes_html += '</select>';
				});
								
				$('#gift_product_list #gift_product').html(products_found);
				$('#gift_attributes_list #gift_attributes_list_select').html(attributes_html);
				displayProductAttributes();
			}
			else
			{
				$('#products_found').hide();
				$('#products_err').html(res.notfound);
				$('#products_err').show();
			}
		}
	});
}

function displayProductAttributes()
{
	if ($('#ipa_' + $('#gift_product option:selected').val() + ' option').length === 0)
		$('#gift_attributes_list').hide();
	else
	{
		$('#gift_attributes_list').show();
		$('.id_product_attribute').hide();
		$('#ipa_' + $('#gift_product option:selected').val()).show();
	}
}
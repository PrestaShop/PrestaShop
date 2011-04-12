/*
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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function CMSCategory_js(value, secure_key)
{
	$.ajax({
		type: "POST",
		url: '../modules/blockcms/ajax_blockcms.php',
		data: 'id_cms_category='+value+'&id_cms_block='+$('#id_cms_block').val()+'&action=getCms&secure_key='+secure_key,
		async : false,
		success: function(msg)
			{
				$('#cms_subcategories').html(msg);
			}
	});
}

function checkallCMSBoxes(checked)
{
	if (checked)
		$('.cmsBox').attr('checked', "checked");
	else
		$('.cmsBox').attr('checked', "");
}

function CMSBlocksDnD(secure_key)
{
	$(document).ready(function()
	{
		$("#table_right").tableDnD({
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
			reOrder = ':even';
			if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
				reOrder = ':odd';
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {
			if (originalOrder != $.tableDnD.serialize())
			{
				var tableDrag = $('#' + table.id);
				$.ajax({
					type: 'POST',
					async: false,
					url: '../modules/blockcms/ajax_blockcms.php?' + $.tableDnD.serialize(),
					data: 'action=dnd&secure_key='+secure_key,
					success: function(data) {
						tableDrag.find('tbody tr').removeClass('alt_row');
						tableDrag.find('tbody tr' + reOrder).addClass('alt_row');
						tableDrag.find('tbody td.positions').each(function(i) {
							$(this).html(i+1);
						});
						tableDrag.find('tbody td.dragHandle a:hidden').show();
						tableDrag.find('tbody td.dragHandle:last a:even').hide();
						tableDrag.find('tbody td.dragHandle:first a:odd').hide();
						var reg = /_[0-9]$/g;
						tableDrag.find('tbody tr').each(function(i) {
							$(this).attr('id', $(this).attr('id').replace(reg, '_' + i));
							
							// Update link position
							var up_reg  = new RegExp('position=[-]?[0-9]+&');
							
							// Up links
							$(this).find('td.dragHandle a:odd').attr('href', $(this).find('td.dragHandle a:odd').attr('href').replace(up_reg, 'position='+ (i - 1) +'&'));
							
							// Down links
							$(this).find('td.dragHandle a:even').attr('href', $(this).find('td.dragHandle a:even').attr('href').replace(up_reg, 'position='+ (i + 1) +'&'));
						});
					}
				});
			}
		}
		});
		$("#table_left").tableDnD({
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
			reOrder = ':even';
			if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
				reOrder = ':odd';
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {
			if (originalOrder != $.tableDnD.serialize())
			{
				var tableDrag = $('#' + table.id);
				$.ajax({
					type: 'POST',
					async: false,
					url: '../modules/blockcms/ajax_blockcms.php?' + $.tableDnD.serialize(),
					data: 'action=dnd&secure_key='+secure_key,
					success: function(data) {
						tableDrag.find('tbody tr').removeClass('alt_row');
						tableDrag.find('tbody tr' + reOrder).addClass('alt_row');
						tableDrag.find('tbody td.positions').each(function(i) {
							$(this).html(i+1);
						});
						tableDrag.find('tbody td.dragHandle a:hidden').show();
						tableDrag.find('tbody td.dragHandle:last a:even').hide();
						tableDrag.find('tbody td.dragHandle:first a:odd').hide();
						var reg = /_[0-9]$/g;
						tableDrag.find('tbody tr').each(function(i) {
							$(this).attr('id', $(this).attr('id').replace(reg, '_' + i));
							
							// Update link position
							var up_reg  = new RegExp('position=[-]?[0-9]+&');
							
							// Up links
							$(this).find('td.dragHandle a:odd').attr('href', $(this).find('td.dragHandle a:odd').attr('href').replace(up_reg, 'position='+ (i - 1) +'&'));
							
							// Down links
							$(this).find('td.dragHandle a:even').attr('href', $(this).find('td.dragHandle a:even').attr('href').replace(up_reg, 'position='+ (i + 1) +'&'));
						});
					}
				});
			}
		}
		});
	});
}

/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function() {
	$('table.tableDnD').tableDnD({
	
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
			reOrder = ':even';
			if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
				reOrder = ':odd';
			$('#'+table.id+ '#' + row.id).parent('tr').addClass('myDragClass');
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {
			if (originalOrder != $.tableDnD.serialize()) {
			
				var way = (originalOrder.indexOf(row.id) < $.tableDnD.serialize().indexOf(row.id))? 1 : 0;
				var ids = row.id.split('_');
				var tableDrag = $('#' + table.id);
				var params = '';
				if (table.id == 'cms_category')
					params = {
						ajaxCMSCategoriesPositions: true,
						id_cms_category_parent: ids[1],
						id_cms_category_to_move: ids[2],
						way: way,
						token: token
					};
				if (table.id == 'category')
					params = {
						ajaxCategoriesPositions: true,
						id_category_parent: ids[1],
						id_category_to_move: ids[2],
						way: way,
						token: token
					};
				if (table.id == 'cms')
					params = {
						ajaxCMSPositions: true,
						id_cms_category: ids[1],
						id_cms: ids[2],
						way: way,
						token: token
					};
				if (come_from == 'AdminModulesPositions')
					params = {
						ajaxModulesPositions: true,
						id_hook: ids[0],
						id_module: ids[1],
						way: way,
						token: token
					};
				if (table.id == 'product') {
					params = {
						ajaxProductsPositions: true,
						id_category: ids[1],
						id_product: ids[2],
						way: way,
						token: token
					};
				}

				$.ajax({
					type: 'POST',
					async: false,
					url: 'ajax.php?' + $.tableDnD.serialize(),
					data: params,
					success: function(data) {
					if (come_from == 'AdminModulesPositions') {
							tableDrag.find('tr').removeClass('alt_row');
							tableDrag.find('tr' + reOrder).addClass('alt_row');
							tableDrag.find('td.positions').each(function(i) {
								$(this).html(i+1);
							});
							tableDrag.find('td.dragHandle a:hidden').show();
							tableDrag.find('td.dragHandle:first a:even').hide();
							tableDrag.find('td.dragHandle:last a:odd').hide();
						}
						else if (table.id == 'product') {
							var reg = /_[0-9][0-9]*$/g;
							tableDrag.find('tbody tr').each(function(i) {
								$(this).attr('id', $(this).attr('id').replace(reg, '_' + i));
								
								// Update link position
								var up_reg  = new RegExp('position=[-]?[0-9]+&');
								
								// Up links
								$(this).find('td.dragHandle a:odd').attr('href', $(this).find('td.dragHandle a:odd').attr('href').replace(up_reg, 'position='+ (i - 1) +'&'));
								
								// Down links
								$(this).find('td.dragHandle a:even').attr('href', $(this).find('td.dragHandle a:even').attr('href').replace(up_reg, 'position='+ (i + 1) +'&'));
								
						});
							tableDrag.find('tr').not('.nodrag').removeClass('alt_row');
							tableDrag.find('tr:not(".nodrag"):odd').addClass('alt_row');
							tableDrag.find('tr td.dragHandle a:hidden').show();
							
							if (alternate) {
								tableDrag.find('tr td.dragHandle:first a:odd').hide();
								tableDrag.find('tr td.dragHandle:last a:even').hide();
							}
							else {
								tableDrag.find('tr td.dragHandle:first a:even').hide();
								tableDrag.find('tr td.dragHandle:last a:odd').hide();
							}
						}
						else 
						{
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
							
							tableDrag.find('tr').not('.nodrag').removeClass('alt_row');
							tableDrag.find('tr:not(".nodrag"):odd').addClass('alt_row');
							tableDrag.find('tr td.dragHandle a:hidden').show();
							
							if (alternate) {
								tableDrag.find('tr td.dragHandle:first a:odd').hide();
								tableDrag.find('tr td.dragHandle:last a:even').hide();
							}
							else {
								tableDrag.find('tr td.dragHandle:first a:even').hide();
								tableDrag.find('tr td.dragHandle:last a:odd').hide();
							}

						}
					}
				});
			}
		}
	});
})

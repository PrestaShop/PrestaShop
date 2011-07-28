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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var readyToExpand = true;
var needCheckAll = false;
var needUncheckAll = false;
var interval = null;
var id = 0;
var arrayCatToExpand = new Array();

$(document).ready(function(){
	$("#categories-treeview").treeview({
		url : 'ajax.php',
		toggle: function () { callbackToggle($(this)); },
		ajax : {
			type: 'POST',
			async: true,
			data: {
				getChildrenCategories : true,
				selectedCat: selectedCat
			}
		}
	});
	
	$('#categories-treeview li#1 span').trigger('click');
	
	$('#expand_all').click( function () {
		expandAllCategories();
		return false;
	});
	
	$('#collapse_all').click( function () {
		collapseAllCategories();
		return false;
	});
	
	$('#check_all').click( function () {
		needCheckAll = true;
		checkAllCategories();
		return false;
	});
	
	$('#uncheck_all').click( function () {
		needUncheckAll = true;
		uncheckAllCategories();
		return false;
	});	
});

function callbackToggle(element)
{
	if (!element.is('.expandable'))
		return false;
	
	if (element.children('ul').children('li.collapsable').length != 0)
		closeChildrenCategories(element);
}

function closeChildrenCategories(element)
{
	var arrayLevel = new Array();
	
	if (element.children('ul').find('li.collapsable').length == 0)
		return false;
	
	element.children('ul').find('li.collapsable').each(function() {
		var level = $(this).children('span.category_level').html();
		if (arrayLevel[level] == undefined)
			arrayLevel[level] = new Array();
		
		arrayLevel[level].push($(this).attr('id'));
	});
	
	for(i=arrayLevel.length-1;i!=0;i--)
		if (arrayLevel[i] != undefined)
			for(j=0;j<arrayLevel[i].length;j++)
			{
				$('li#'+arrayLevel[i][j]+'.collapsable').children('span.category_label').trigger('click');
				$('li#'+arrayLevel[i][j]+'.expandable').children('ul').hide();
			}
}

function setCategoryToExpand()
{
	var ret = false;
	
	id = 0;
	arrayCatToExpand = new Array();
	$('li.expandable:visible').each(function() {
		arrayCatToExpand.push($(this).attr('id'));
		ret = true;
	});
	
	return ret;
}

function needExpandAllCategories()
{
	return $('li').is('.expandable');
}

function expandAllCategories()
{
	// if no category to expand, no action
	if (!needExpandAllCategories())
		return;
	readyToExpand = true;
	if (setCategoryToExpand())
		interval = setInterval(openCategory, 10);
}

function openCategory()
{
	// Check readyToExpand in order to don't clearInterval if AJAX request is in progress
	// readyToExpand = category has been expanded, go to next ;)
	if (id >= arrayCatToExpand.length && readyToExpand)
	{
		if (!setCategoryToExpand())
		{
			clearInterval(interval);
			// delete interval value
			interval = null;
			readyToExpand = false;
			if (needCheckAll)
			{
				checkAllCategories();
				needCheckAll = false;
			}
			else if (needUncheckAll)
			{
				uncheckAllCategories();
				needUncheckAll = false;
			}
		}
		else
			readyToExpand = true;
	}
	
	if (readyToExpand)
	{
		if ($('li#'+arrayCatToExpand[id]+'.hasChildren').length > 0)
			readyToExpand = false;
		$('li#'+arrayCatToExpand[id]+'.expandable:visible span.category_label').trigger('click');
		id++;
	}
}

function collapseAllCategories()
{
	closeChildrenCategories($('li#1'));
}

function checkAllCategories()
{
	if (needExpandAllCategories())
		expandAllCategories();
	else
	{
		$('input[name="categoryBox[]"]').not(':checked').each(function () {
			$(this).attr('checked', 'checked');
			clickOnCategoryBox($(this));
		});
	}
}

function uncheckAllCategories()
{
	if (needExpandAllCategories())
		expandAllCategories();
	else
	{
		$('input[name="categoryBox[]"]:checked').each(function () { 
			$(this).removeAttr('checked');
			clickOnCategoryBox($(this));
		});
	}
}

function clickOnCategoryBox(category)
{
	if (category.is(':checked'))
	{
		$('select#id_category_default').append('<option value="'+category.val()+'">'+(category.val() !=1 ? category.parent().find('span').html() : home)+'</option>');
		updateNbSubCategorySelected(category, true);
		if ($('select#id_category_default option').length > 0)
		{
			$('select#id_category_default').show();
			$('#no_default_category').hide();
		}
	}
	else
	{
		$('select#id_category_default option[value='+category.val()+']').remove();
		updateNbSubCategorySelected(category, false);
		if ($('select#id_category_default option').length == 0)
		{
			$('select#id_category_default').hide();
			$('#no_default_category').show();
		}
	}
}

function updateNbSubCategorySelected(category, add)
{
	var currentSpan = category.parent().parent().parent().children('.nb_sub_cat_selected');
	var parentNbSubCategorySelected = currentSpan.children('.nb_sub_cat_selected_value').html();

	if (add)
		var newValue = parseInt(parentNbSubCategorySelected)+1;
	else
		var newValue = parseInt(parentNbSubCategorySelected)-1;
	
	currentSpan.children('.nb_sub_cat_selected_value').html(newValue);
	currentSpan.children('.nb_sub_cat_selected_word').html(selectedLabel);
	
	if (newValue == 0)
		currentSpan.hide();
	else
		currentSpan.show();
	
	if (currentSpan.parent().children('.nb_sub_cat_selected').length != 0)
		updateNbSubCategorySelected(currentSpan.parent().children('input'), add);
}
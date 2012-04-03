/*
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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Handles loading of product tabs
 */
function ProductTabsManager(){
	this.product_tabs = [];
	var self = this;

	this.setTabs = function(tabs){
		this.product_tabs = tabs;
	}

	/**
	 * Schedule execution of onReady() function for each tab
	 */
	this.onReady = function(){
		for (var tab_name in this.product_tabs)
		{
			if (this.product_tabs[tab_name].onReady !== undefined)
				this.onLoad(tab_name, this.product_tabs[tab_name].onReady);
		}
	}

	/**
	 * Execute a callback function when a specific tab has finished loading or right now if the tab has already loaded
	 *
	 * @param tab_name name of the tab that is checked for loading
	 * @param callback_function function to call
	 */
	this.onLoad = function (tab_name, callback)
	{
		container = $('#product-tab-content-' + tab_name);
		if (container.length === 0)
			throw 'Could not find container for tab name: ' + tab_name;

		// onReady() is always called after the dom has been created for the tab (similar to $(document).ready())
		if (container.hasClass('not-loaded'))
			container.bind('loaded', callback);
		else
			callback();
	}

	/**
	 * Get a single tab or recursively get tabs in stack then display them
	 *
	 * @param int id position of the tab in the product page
	 * @param boolean selected is the tab selected
	 * @param int index current index in the stack (or 0)
	 * @param array stack list of tab ids to load (or null)
	 */
	this.display = function (id, selected, index, stack)
	{
		var myurl = $('#link-'+id).attr("href")+"&ajax=1";
		var tab_selector = $("#product-tab-content-"+id);
		// Used to check if the tab is already in the process of being loaded
		tab_selector.addClass('loading');

		if (selected)
			$('#product-tab-content-wait').show();

		$.ajax({
			url : myurl,
			async : true,
			cache: false, // cache needs to be set to false or IE will cache the page with outdated product values
			type: 'POST',
			success : function(data)
			{
				tab_selector.html(data);
				tab_selector.removeClass('not-loaded');

				if (selected)
				{
					$("#link-"+id).addClass('selected');
					tab_selector.show();
				}
			},
			complete : function(data)
			{
				$("#product-tab-content-"+id).removeClass('loading');
				if (selected)
				{
					$('#product-tab-content-wait').hide();
					tab_selector.trigger('displayed');
				}
				tab_selector.trigger('loaded');

				if (stack && stack[index + 1])
					self.display(stack[index + 1], selected, index + 1, stack);
			},
			beforeSend : function(data)
			{
				// don't display the loading notification bar
				if (typeof(ajax_running_timeout) !== 'undefined')
					clearTimeout(ajax_running_timeout);
			}
		});
	}
}
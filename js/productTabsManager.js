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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Handles loading of product tabs
 */
function ProductTabsManager(){
	var self = this;
	this.product_tabs = [];
	this.current_request;
	this.stack_error = [];
	this.page_reloading = false;

	this.setTabs = function(tabs){
		this.product_tabs = tabs;
	}

	/**
	 * Schedule execution of onReady() function for each tab and bind events
	 */
	this.init = function(){
		for (var tab_name in this.product_tabs)
		{
			if (this.product_tabs[tab_name].onReady !== undefined)
				this.onLoad(tab_name, this.product_tabs[tab_name].onReady);
		}

		// @see with tDidierjean
		//$(document).bind('change', function(){
		//	if (self.current_request)
		//	{
		//		self.current_request.abort();
		//	}
		//});

		$('.shopList.chzn-done').bind('change', function(){
			if (self.current_request)
			{
				self.page_reloading = true;
				self.current_request.abort();
			}
		});

		$(window).bind('beforeunload', function() {
			self.page_reloading = true;
		});
	}

	/**
	 * Execute a callback function when a specific tab has finished loading or right now if the tab has already loaded
	 *
	 * @param tab_name name of the tab that is checked for loading
	 * @param callback_function function to call
	 */
	this.onLoad = function (tab_name, callback)
	{
		var container = $('#product-tab-content-' + tab_name);
		// Some containers are not loaded depending on the shop configuration
		if (container.length === 0)
			return;

		// onReady() is always called after the dom has been created for the tab (similar to $(document).ready())
		if (container.hasClass('not-loaded'))
			container.bind('loaded', callback);
		else
			callback();
	}

	/**
	 * Get a single tab or recursively get tabs in stack then display them
	 *
	 * @param string tab_name name of the tab
	 * @param boolean selected is the tab selected
	 */
	this.display = function (tab_name, selected)
	{
		var tab_selector = $("#product-tab-content-"+tab_name);

		// Is the tab already being loaded?
		if (!tab_selector.hasClass('not-loaded') || tab_selector.hasClass('loading'))
			return;

		// Mark the tab as being currently loading
		tab_selector.addClass('loading');

		if (selected)
			$('#product-tab-content-wait').show();

		// send $_POST array with the request to be able to retrieve posted data if there was an error while saving product
		var data;
		if (save_error)
		{
			data = post_data;
			// set key_tab so that the ajax call returns the display for the current tab
			data.key_tab = tab_name;
		}

		return $.ajax({
			url : $('#link-'+tab_name).attr("href")+"&ajax=1" + '&rand=' + new Date().getTime(),
			async : true,
			cache: false, // cache needs to be set to false or IE will cache the page with outdated product values
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			data: data,
			success : function(data)
			{
				tab_selector.html(data);
				tab_selector.removeClass('not-loaded');

				if (selected)
				{
					$("#link-"+tab_name).addClass('selected');
					tab_selector.show();
				}
				tab_selector.trigger('loaded');
			},
			complete : function(data)
			{
				tab_selector.removeClass('loading');
				if (selected)
				{
					$('#product-tab-content-wait').hide();
					tab_selector.trigger('displayed');
				}
			},
			beforeSend : function(data)
			{
				// don't display the loading notification bar
				if (typeof(ajax_running_timeout) !== 'undefined')
					clearTimeout(ajax_running_timeout);
			}
		});
	}

	/**
	 * Send an ajax call for each tab in the stack, binding each call to the "complete" event of the previous call
	 *
	 * @param array stack contains tab names as strings
	 */
	this.displayBulk = function(stack){
		this.current_request = this.display(stack[0], false);

		if (this.current_request !== undefined)
		{
			this.current_request.complete(function(request, status) {
				if (status === 'abort' || status === 'error')
					self.stack_error.push(stack.shift());
				else
					stack.shift()
				if (stack.length !== 0 && status !== 'abort')
				{
					self.displayBulk(stack);
				}
				else if (self.stack_error.length !== 0 && !self.page_reloading)
				{
					jConfirm(reload_tab_description, reload_tab_title, function(confirm) {
						if (confirm === true)
						{
							self.displayBulk(self.stack_error.slice(0));
							self.stack_error = [];
						}
						else
							return false;
					});
				}
			});
		}
	}
}
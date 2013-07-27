/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$( '.prestashop-page' ).live( 'pageshow',function(event)
{
	if ($('#stores_search_block .ui-btn').length)
	{
		$('#stores_search_block .ui-btn').live('click', function()
		{
			searchStores();
		});
	}
});
function searchStores()
{
	// gets pattern and radius requested
	var pattern = $('#location').val();
	var radius = $('#radius').val();
	
	// if pattern and radius are valid
	if (pattern != '' && radius > 0 && radius <= 100)
	{
		// as usual, ask Google for the coordinates :)
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({address: pattern}, function(res, code) {
			
			if (code == google.maps.GeocoderStatus.OK)
				getStores(res[0].geometry.location, radius);
			
		});
	}
	else if ($('.stores_block').is(':visible'))
		$('.stores_block').hide();
}

function getStores(coordinates, radius)
{
	// given the coordinates, gets latitude/longitude
	var latitude = coordinates.lat();
	var longitude = coordinates.lng();

	// if parameters are valid, calls the StoresController
	if (latitude != undefined && latitude != undefined && radius != undefined)
	{
		// ajax call
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: 'index.php?controller=stores' + '&rand=' + new Date().getTime(),
			data: 'ajax=true&latitude=' + latitude + '&longitude=' + longitude + '&radius=' + radius,
			dataType: 'json',
			async : true,
			success: function(data) {
				var list = $('#stores_list');
				// hide if nothing to display
				if (data.length == 0)
					list.hide();
				else // constructs <ul>
				{
					// resets the list
					list.html('');
					// for each stores to display
					$.each(data, function(index, element) {
						// constructs the address
						var store_address = '';
						store_address += element.address1 + (element.address2 != null ? element.address2 : ' ') + ', ';
						store_address += element.city + ', ' + element.postcode + (element.state != null ? ' ' + element.state : '') + ', ' + element.country;
						
						// apprends <li>, wrapped in a google map link
						list.append($(
								'<li>' +
								'<a href="http://maps.google.com/maps?q=' + encodeURI(store_address) + '">' + 
								'<img src="' + img_store_dir + parseInt(element.picture) + '.jpg" width="80"/>' +  
								'<h3>'+ element.name +' (' + Math.floor(element.distance) + distance_unit + ')</h3>' +
								'<p>' + store_address + '</p>' + 
								'</a>' + 
								'</li>'))
					});
					
					// display the list and refresh
					list.parent().show();
					list.listview('refresh');
					// scroll
					$('html, body').animate({scrollTop: list.parent().offset().top},'slow');
				}
			},
			error: function(XMLHttpRequest, status) {
			}
		});
	}
}
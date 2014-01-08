/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$('document').ready(function(){
	if (typeof blockHover != 'undefined')
		blockHover();
	if (typeof reloadProductComparison != 'undefined')
		reloadProductComparison();
});
function blockHover(status) {
	$('.product_list.grid li.ajax_block_product').each(function() {
		$(this).find('.product-container').hover(
		function(){
			if ($('body').find('.container').width() == 1170)
			$(this).parent().addClass('hovered'); 
		},
		function(){
			if ($('body').find('.container').width() == 1170)
			$(this).parent().removeClass('hovered');
		}
	)});	
}
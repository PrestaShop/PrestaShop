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

function checkBeforeComparison()
{
	var id_list = '';
	$('.comparator:checked').each(
		function()
		{
			id_list += $(this).val() + '|';
		}
	);

	$('.compare_product_list').val(id_list);

	if ($('.comparator:checked').length == 0)
	{
		alert(min_item);
		return false;
	}

	return true;
}

function checkForComparison(nb_max_item)
{
	if ($('.comparator:checked').length > nb_max_item)
		alert(max_item);
}


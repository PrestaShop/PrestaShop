{*
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
*}
<script>
	function confirmProductAttached(productList)
	{
		var confirm_text = "{l s='This attachment is associated with the following products:'}";
		if (confirm('{$confirm}'))
			return confirm(confirm_text + product_list);
		return false;
	}
</script>

<a href="{$href}" onclick="{if isset($product_attachements[$id])}return confirmProductAttached('{$product_list[$id]}'){else}return confirm('{$confirm}'){/if}">
	<img src="../img/admin/delete.gif" alt="{$action}" title="{$action}" />
</a>


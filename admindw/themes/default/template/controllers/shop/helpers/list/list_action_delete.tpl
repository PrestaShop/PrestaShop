{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9197 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<a href="{$href}" class="delete"
	{if in_array($id_shop, $shops_having_dependencies)}
		onclick="jAlert('{l s='You cannot delete this shop (customer and/or order dependency)'}'); return false;"
	{elseif isset($confirm)}
		onclick="if (confirm('{$confirm}')){ return true; }else{ event.stopPropagation(); event.preventDefault();};"
	{/if} title="{$action}">
	<img src="../img/admin/delete.gif" alt="{$action}" />
</a>
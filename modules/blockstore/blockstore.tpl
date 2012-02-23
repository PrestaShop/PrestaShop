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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block stores module -->
<div id="stores_block_left" class="block">
	<h4><a href="{$link->getPageLink('stores')}" title="{l s='Our stores' mod='blockstore'}">{l s='Our stores' mod='blockstore'}</a></h4>
	<div class="block_content blockstore">
		<p class="store_image"><a href="{$link->getPageLink('stores')}" title="{l s='Our stores' mod='blockstore'}"><img src="{$module_dir}{$store_img}" alt="{l s='Our stores' mod='blockstore'}" width="174" height="115" /></a></p>
		<p>
			<a href="{$link->getPageLink('stores')}" title="{l s='Our stores' mod='blockstore'}">&raquo; {l s='Discover our stores' mod='blockstore'}</a>
		</p>
	</div>
</div>
<!-- /Block stores module -->

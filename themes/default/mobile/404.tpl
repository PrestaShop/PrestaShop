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

{capture assign='page_title'}{l s='This page is not available'}{/capture}
{include file='./page-title.tpl'}

	{* Submit à tester sur téléphone *}
	{* ===================================== *}
	<div data-role="content" id="content">
		<div id="not_found">
			<p>{l s='We\'re sorry, but the Web address you\'ve entered is no longer available.'}</p>
			<p>{l s='To find a product, please type its name in the field below.'}</p>
			<div data-role="fieldcontain" class="input_search_404">
				<form action="{$link->getPageLink('search')}" method="post" class="std">
				<input type="search" name="search_query" id="search_query" value="{l s='Search'}" />
				</form>
			</div>
			<p>
				<a href="{$base_dir}" class="lnk_my-account_home" title="{l s='Home'}" data-ajax="false">
					<img class="" alt="{l s='Home'}" src="{$img_mobile_dir}icon/home.png">
					{l s='Home'}
				</a>
			</p>
		</div>
	{* ===================================== *}
	</div><!-- /content -->

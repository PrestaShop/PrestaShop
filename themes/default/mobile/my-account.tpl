{*
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
*  @version  Release: $Revision: 6599 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture assign='page_title'}{l s='My account'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="content">
		<p>{l s='Welcome to your account. Here, you can manage your addresses and orders.'}</p>
		
		<ul data-role="listview" data-inset="true" id="list_myaccount">
			{if $has_customer_an_address}
			<li>
				<a href="{$link->getPageLink('address', true)}" title="{l s='Add my first address'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/addrbook.png" alt="{l s='Addresses'}" class="ui-li-icon ui-li-thumb" />
					{l s='Add my first address'}
				</a>
			</li>
			{/if}
			<li>
				<a href="{$link->getPageLink('history', true)}" title="{l s='Orders'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/order.png" alt="{l s='Orders'}" class="ui-li-icon ui-li-thumb" />
					{l s='History and details of my orders'}
				</a>
			</li>
			{if $returnAllowed}
			<li>
				<a href="{$link->getPageLink('order-follow', true)}" title="{l s='Merchandise returns'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/return.png" alt="{l s='Merchandise returns'}" class="ui-li-icon ui-li-thumb" />
					{l s='My merchandise returns'}
				</a>
			</li>
			{/if}
			<li>
				<a href="{$link->getPageLink('order-slip', true)}" title="{l s='Credit slips'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/slip.png" alt="{l s='Credit slips'}" class="ui-li-icon ui-li-thumb" />
					{l s='My credit slips'}
				</a>
			</li>
			<li>
				<a href="{$link->getPageLink('addresses', true)}" title="{l s='Addresses'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/addrbook.png" alt="{l s='Addresses'}" class="ui-li-icon ui-li-thumb" />
					{l s='My addresses'}
				</a>
			</li>
			<li>
				<a href="{$link->getPageLink('identity', true)}" title="{l s='Information'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/userinfos.png" alt="{l s='Information'}" class="ui-li-icon ui-li-thumb" />
					{l s='My personal information'}
				</a>
			</li>
			{if $voucherAllowed}
			<li>
				<a href="{$link->getPageLink('discount', true)}" title="{l s='Vouchers'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/voucher.png" alt="{l s='Vouchers'}" class="ui-li-icon ui-li-thumb" />
					{l s='My vouchers'}
				</a>
			</li>
			{/if}
			<li data-icon="delete" data-theme="a">
				<a href="{$link->getPageLink('index', true)}?mylogout" title="{l s='Sign out'}" data-ajax="false">
					{l s='Sign out'}
				</a>
			</li>
			{* Un hook est dans la liste (pour favoris et wishlist) *}
			{* ===================================== *}
			{hook h="mobileCustomerAccount"}
			{* ===================================== *}
		</ul>

		<a href="{$base_dir}" class="lnk_my-account_home" title="{l s='Home'}" data-ajax="false">
			<img class="" alt="{l s='Home'}" src="{$img_mobile_dir}icon/home.png">
			{l s='Home'}
		</a>
		{include file='./sitemap.tpl'}
	</div><!-- /content -->

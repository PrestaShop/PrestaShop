{*
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
*}

{assign var=module_name value='<strong><span class="module-name-placeholder"></span></strong>'}

<div class="modal-body">
	<h1><span class="icon icon-exclamation-sign"></span> {l s='This module is Untrusted'}</h1>

	<h4 style="margin:24px 0;">{l s='You are about to install the module %s that couldn\'t be identified.' sprintf=$module_name }</h4>
	 
	<p>{l s='This generally happens when the module isn\'t distributed through our official marketplace, PrestaShop Addons - or when your server failed to communicate with PrestaShop Addons.
	Downloading modules outsides PrestaShop Addons means we couldn\'t verified that the module is not adding some undisclosed functionality like backdoors, ads, hidden links, and spam.'}
	<strong>{l s='Please use at our own risk.'}</strong></p>

	<h3>{l s='What Should I Do?'}</h3>
	<p>{l s='If you understand what this module can do, you can still proceed with the installation.'}</p>

	<p>{l s='If you are unsure about its content'}, <a href="{$catalog_link}">{l s='you can browse PrestaShop Addons for similar modules.'}</a></p>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-info btn-lg" data-dismiss="modal">{l s='Back to safety'}</button>
	<a id="proceed-install-anyway" href="#" class="btn btn-warning btn-lg">{l s='Proceed with the installation'}</a>
</div>
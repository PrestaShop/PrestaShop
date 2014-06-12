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

	<div class="alert alert-warning">
		<h4>{l s='You are about to install "%s", a module that could not be securely identified by PrestaShop.' sprintf=$module_name}</h4>

		<p>{l s='This generally happens when the module is not distributed through our official marketplace, PrestaShop Addons - or when your server failed to communicate with PrestaShop Addons.'}
		{l s='Since you did not download the module from PrestaShop Addons, we cannot assert that the module is safe (i.e. that it is not adding some undisclosed functionality like backdoors, ads, hidden links, spam, etc.).'}
		<strong>{l s='Use at your own risk.'}</strong></p>
	</div>

	<h3>{l s='What Should I Do?'}</h3>
	<p>{l s='If you understand what this module does and you are certain that it is safe, you can proceed with the installation.'}</p>

	<p>{l s='If you are unsure about the safety of this module, you can look for similar modules on the official marketplace.'} <a class="catalog-link" target="_blank" href="#">{l s='Click here to browse PrestaShop Addons.'}</a></p>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Back to safety'}</button>
	<a id="proceed-install-anyway" href="#" class="btn btn-warning">{l s='Proceed with the installation'}</a>
</div>

{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="block-contact col-md-4 col-md-offset-2">
	<h4 class="_gray-darker text-uppercase block-contact-title">{l s='Store information' mod='blockcontact'}</h4>
    {$contact_infos.address.formatted nofilter}
    {if $contact_infos.phone}<br>{l s='Tel: %s' sprintf=$contact_infos.phone}{/if}
    {if $contact_infos.fax}<br>{l s='Fax: %s' sprintf=$contact_infos.fax}{/if}
    {if $contact_infos.email}<br>{l s='Email: %s' sprintf=$contact_infos.email}{/if}
</div>

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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="contact_block" class="block">
	<h4>{l s='Contact us' mod='blockcontact'}</h4>
	<div class="block_content clearfix">
			<p>{l s='Our hotline' mod='blockcontact'}<br />{l s='is available 24/7' mod='blockcontact'}</p>
			{if $telnumber != ''}<p class="tel">{l s='Phone : ' mod='blockcontact'}{$telnumber}</p>{/if}
			{if $email != ''}<a href="mailto:{$email}">{l s='Contact' mod='blockcontact'}<br/> {l s='our hotline' mod='blockcontact'}</a>{/if}
		</form>
	</div>
</div>

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

<hr width="99%" align="center" size="2" />
<!-- Block Mobile Newsletter module-->
<div id="newsletter">
	<h4>{l s="Newsletter" mod="blocknewletter"}</h4>
	{if isset($msg) && $msg}
		<p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>
	{/if}
	<form action="{$link->getPageLink('index')}" method="post">
		<fieldset>
			<input type="email" name="email"
				   value="{if isset($value) && $value}{$value}{else}{l s='your e-mail' mod='blocknewsletter'}{/if}"
				   onfocus="javascript:if(this.value=='{l s='your e-mail' mod='blocknewsletter'}')this.value='';"
				   onblur="javascript:if(this.value=='')this.value='{l s='your e-mail' mod='blocknewsletter'}';"
				   class="inputNew" />
			<input type="submit" value="OK" data-role="button" data-theme="a" name="submitNewsletter" />
			<input type="hidden" name="action" value="0" />
		</fieldset>
	</form>
</div><!-- /newsletter -->
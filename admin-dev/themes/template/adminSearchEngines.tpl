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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($tab_form)}
	<form action="{$tab_form['current']}&submitAdd{$tab_form['table']}=1&token={$tab_form['token']}" method="post">
		{if $tab_form['id']}<input type="hidden" name="id_{$tab_form['table']}" value="{$tab_form['id']}" />{/if}
		<fieldset>
			<legend>{l s ='Referrer'}</legend>

			<label>{l s ='Server'} </label>
			<div class="margin-form">
				<input type="text" size="20" name="server" value="{$tab_form['server']}" /> <sup>*</sup>
			</div>
			<label>{l s ='$_GET variable'} </label>
			<div class="margin-form">
				<input type="text" size="40" name="getvar" value="{$tab_form['getvar']}" /> <sup>*</sup>
			</div>
			<div class="margin-form">
				<input type="submit" value="{l s ='   Save   '}" name="submitAdd{$tab_form['table']}" class="button" />
			</div>
			<div class="small"><sup>*</sup> {l s ='Required field'}</div>
		</fieldset>
	</form>

{/if}

{$content}
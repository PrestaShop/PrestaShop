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
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $existingExport}
	<div class="hint" style="display:block;">
		{l s='The last export has been found for this section:'}
		<a href="{$smarty.server.REQUEST_URI}&download={$type}">{l s='Click here'}</a>
	</div>
	<br />
{/if}

<label for="clientPrefix">{l s='Client prefix:'}</label>
<div class="margin-form">
	<input type="text" value="{$clientPrefix|htmlentities}" name="clientPrefix" />
	<span class="input-error"></span>
</div>

<label for="beginDate">{l s='Begin to:'}</label>
<div class="margin-form">
	<input class="datepicker" id="beginDate_{$type}" type="text" name="beginDate" value="{$begin_date}" />
	<span class="input-error">{l s='The date has not the right format'}</span>
</div>

<label for="endDate">{l s='End to:'}</label>
<div class="margin-form">
	<input class="datepicker" id="endDate_{$type}" type="text" name="endDate" value="{$end_date}" />
	<span class="input-error">{l s='The date has not the right format'}</span>
</div>

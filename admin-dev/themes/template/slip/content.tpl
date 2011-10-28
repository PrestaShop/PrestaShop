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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{$content}

<h2>{l s='Print PDF'}</h2>
<fieldset style="float:left;width:300px;margin-bottom:20px"><legend><img src="../img/admin/pdf.gif" alt="" /> {l s='By date'}</legend>
	<form action="{$url_post}" method="post">
		<label style="width:90px">{l s='From:'} </label>
		<div class="margin-form" style="padding-left:100px">
			<input type="text" size="4" maxlength="10" name="date_from" value="{$date}" style="width: 120px;" /> <sup>*</sup>
			<p class="clear">{l s='Format: 2007-12-31 (inclusive)'}</p>
		</div>
		<label style="width:90px">{l s='To:'} </label>
		<div class="margin-form" style="padding-left:100px">
			<input type="text" size="4" maxlength="10" name="date_to" value="{$date}" style="width: 120px;" /> <sup>*</sup>
			<p class="clear">{l s='Format: 2008-12-31 (inclusive)'}</p>
		</div>
		<div class="margin-form" style="padding-left:100px">
			<input type="submit" value="{l s='Generate PDF file'}" name="submitPrint" class="button" />
		</div>
	</form>
</fieldset>



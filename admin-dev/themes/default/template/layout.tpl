{**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{$header}
{if isset($conf)}
	<div class="bootstrap">
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{$conf}
		</div>
	</div>
{/if}
{if isset($error)}
  <div class="bootstrap">
    <div class="alert alert-danger">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {$error}
    </div>
  </div>
{/if}
{if count($errors) && current($errors) != '' && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}

	<div class="bootstrap">
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if count($errors) == 1}
			{reset($errors)}
		{else }
			{l s='There are %d errors.' sprintf=[$errors|count] d='Admin.Notifications.Error'}
			<br/>
			<ol>
				{foreach $errors as $error}
					<li>{$error}</li>
				{/foreach}
			</ol>
		{/if}
		</div>
	</div>
{/if}
{if isset($informations) && count($informations) && $informations}
	<div class="bootstrap">
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<ul id="infos_block" class="list-unstyled">
				{foreach $informations as $info}
					<li>{$info}</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}
{if isset($confirmations) && count($confirmations) && $confirmations}
	<div class="bootstrap">
		<div class="alert alert-success" style="display:block;">
			{foreach $confirmations as $conf}
				{$conf}
			{/foreach}
		</div>
	</div>
{/if}
{if count($warnings)}
	<div class="bootstrap">
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{if count($warnings) > 1}
				<h4>{l s='There are %d warnings:' sprintf=[$warnings|count]}</h4>
			{/if}
			<ul class="list-unstyled">
				{foreach $warnings as $warning}
					<li>{$warning}</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}
{$page}
<div class="mobile-layer"></div>
{$footer}

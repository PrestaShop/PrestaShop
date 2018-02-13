{**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file="helpers/form/form.tpl"}

{block name="script"}
	var ps_force_friendly_product = false;
{/block}

{block name="input"}
	{if $input.name == "link_rewrite"}
		<script type="text/javascript">
		{if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}
			var PS_ALLOW_ACCENTED_CHARS_URL = 1;
		{else}
			var PS_ALLOW_ACCENTED_CHARS_URL = 0;
		{/if}
		</script>
		{$smarty.block.parent}
	{else}
		{$smarty.block.parent}
	{/if}
	{if in_array($input.name, ['image', 'thumb'])}
		<div class="col-lg-6">
			<div class="help-block">{l s='Recommended dimensions (for the default theme): %1spx x %2spx' sprintf=[$input.format.width, $input.format.height] d='Admin.Catalog.Help'}
			</div>
		</div>
	{/if}
{/block}
{block name="description"}
	{$smarty.block.parent}
	{if ($input.name == 'groupBox')}
		<div class="alert alert-info">
			<h4>{$input.info_introduction}</h4>
			<p>{$input.unidentified}<br />
			{$input.guest}<br />
			{$input.customer}</p>
		</div>
	{/if}
{/block}
{block name="input_row"}
	{$smarty.block.parent}
	{if ($input.name == 'thumbnail')}
		{$displayBackOfficeCategory}
	{/if}
{/block}

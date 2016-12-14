{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-group"></i> {l s='Group information'}</h3>
			<h2><i class="icon-group"></i> {$group->name[$language->id]}</h2>
			<div class="form-horizontal">
				<div class="form-group">
					<label class="col-lg-3 control-label">{l s='Discount:'}</label>
					<div class="col-lg-3"><p class="form-control-static">{l s='Discount: %.2f%%' sprintf=[$group->reduction]}</p></div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">{l s='Price display method:'}</label>
					<div class="col-lg-3"><p class="form-control-static">{if $group->price_display_method}
					{l s='Tax excluded'}
				{else}
					{l s='Tax included'}
				{/if}</p></div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">{l s='Show prices:'}</label>
					<div class="col-lg-3"><p class="form-control-static">{if $group->show_prices}{l s='Yes' d='Admin.Global'}{else}{l s='No' d='Admin.Global'}{/if}</p></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-dollar"></i> {l s='Current category discount'}</h3>
			{if !$categorieReductions}
				<div class="alert alert-warning">{l s='None'}</div>
			{else}
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box">{l s='Category'}</span></th>
							<th><span class="title_box">{l s='Discount'}</span></th>
						</tr>
					</thead>
					<tbody>
					{foreach $categorieReductions key=key item=category }
						<tr class="alt_row">
							<td>{$category.path}</td>
							<td>{l s='Discount: %.2f%%' sprintf=[$category.reduction]}</td>
						</tr>
					{/foreach}
					<tbody>
				</table>
			{/if}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<h2>{l s='Members of this customer group'}</h2>
		<p>{l s='Limited to the first 100 customers.'} {l s='Please use filters to narrow your search.'}</p>
		{$customerList}
	</div>
</div>
{/block}

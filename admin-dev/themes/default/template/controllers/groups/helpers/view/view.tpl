{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-group"></i> {l s='Group information' d='Admin.Shopparameters.Feature'}</h3>
			<h2><i class="icon-group"></i> {$group->name[$language->id]|escape:'html':'UTF-8'}</h2>
			<div class="form-horizontal">
				<div class="form-group">
					<label class="col-lg-3 control-label">{l s='Discount:'}</label>
					<div class="col-lg-3"><p class="form-control-static">{l s='Discount: %.2f%%' sprintf=[$group->reduction] d='Admin.Shopparameters.Feature'}</p></div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">{l s='Price display method:' d='Admin.Shopparameters.Feature'}</label>
					<div class="col-lg-3"><p class="form-control-static">{if $group->price_display_method}
					{l s='Tax excluded' d='Admin.Global'}
				{else}
					{l s='Tax included' d='Admin.Global'}
				{/if}</p></div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">{l s='Show prices:' d='Admin.Shopparameters.Feature'}</label>
					<div class="col-lg-3"><p class="form-control-static">{if $group->show_prices}{l s='Yes' d='Admin.Global'}{else}{l s='No' d='Admin.Global'}{/if}</p></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-dollar"></i> {l s='Current category discount' d='Admin.Shopparameters.Feature'}</h3>
			{if !$categorieReductions}
				<div class="alert alert-warning">{l s='None' d='Admin.Global'}</div>
			{else}
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box">{l s='Category' d='Admin.Global'}</span></th>
							<th><span class="title_box">{l s='Discount' d='Admin.Global'}</span></th>
						</tr>
					</thead>
					<tbody>
					{foreach $categorieReductions key=key item=category }
						<tr class="alt_row">
							<td>{$category.path|escape:'html':'UTF-8'}</td>
							<td>{l s='Discount: %.2f%%' sprintf=[$category.reduction] d='Admin.Shopparameters.Feature'}</td>
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
		<h2>{l s='Members of this customer group' d='Admin.Shopparameters.Feature'}</h2>
		<p>{l s='Limited to the first 100 customers.' d='Admin.Shopparameters.Feature'} {l s='Please use filters to narrow your search.' d='Admin.Shopparameters.Feature'}</p>
		{$customerList}
	</div>
</div>
{/block}

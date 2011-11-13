{extends file="helper/options/options.tpl"}
{block name="after"}
<br />
{if $localizations_pack}
<form method="post" action="{$current}&amp;token={$token}" class="width2" enctype="multipart/form-data">
	<fieldset>
	<legend><img src="../img/admin/localization.gif" />{l s='Localization pack import'}</legend>
	<div style="clear: both; padding-top: 15px;">
	<label>{l s='Localization pack you want to import:'}</label>

		<div class="margin-form">
		<select id="iso_localization_pack" name="iso_localization_pack">
		{foreach from=$localizations_pack key=iso item=name}
			<option value="{$iso}">{$name}</option>
		{/foreach}
		</select>
		</div>
			<br />
				<label>{l s='Content to import:'}</label>
				<div class="margin-form" style="padding-top: 5px;">
					<input type="checkbox" name="selection[]" value="states" checked="checked" /> {l s='States'}<br />
					<input type="checkbox" name="selection[]" value="taxes" checked="checked" /> {l s='Taxes'}<br />
					<input type="checkbox" name="selection[]" value="currencies" checked="checked" /> {l s='Currencies'}<br />
					<input type="checkbox" name="selection[]" value="languages" checked="checked" /> {l s='Languages'}<br />
					<input type="checkbox" name="selection[]" value="units" checked="checked" /> {l s='Units (e.g., weight, volume, distance)'}
				</div>
				<div align="center" style="margin-top: 20px;">
					<input type="submit" class="button" name="submitLocalizationPack" value="{l s='   Import   '}" />
				</div>
			</div>
		</fieldset>
		</form>
		<br />
{else}
<p class="warn">{l s='Cannot connect to prestashop.com'}</p>
{/if}
{/block}

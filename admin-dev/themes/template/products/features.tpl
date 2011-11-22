<table cellpadding="5">
	<tr>
		<td colspan="2">
			<b>{l s='Assign features to this product:'}</b><br />
			<ul style="margin: 10px 0 0 20px;">
				<li>{l s='You can specify a value for each relevant feature regarding this product, empty fields will not be displayed.'}</li>
				<li>{l s='You can either set a specific value, or select among existing pre-defined values you added previously.'}</li>
			</ul>
		</td>
	</tr>
</table>
<div class="separation"></div><br />


<table border="0" cellpadding="0" cellspacing="0" class="table" style="width:900px;">
	<tr>
		<th>{l s='Feature'}</td>
		<th style="width:30%">{l s='Pre-defined value'}</td>
		<th style="width:40%"><u>{l s='or'}</u> {l s='Customized value'}</td>
	</tr>
</table>
{foreach from=$available_features item=available_feature}
<table cellpadding="5" style="width: 900px; margin-top: 10px">
<tr>
	<td>{$available_feature.name}</td>
	<td style="width: 30%">
	{if sizeof($available_feature.featureValues)}
		<select id="feature_{$available_feature.id_feature}_value" name="feature_{$available_feature.id_feature}_value"
			onchange="$('.custom_{$available_feature.id_feature}_').val('');">
			<option value="0">---&nbsp;</option>
				{foreach from=$available_feature.featureValues item=value}
					<option value="{$value.id_feature_value}"{if $available_feature.current_item == $value.id_feature_value}selected="selected"{/if} >
						{$value.value|truncate:40}&nbsp;
					</option>
				{/foreach}

		</select>
	{else}
		<input type="hidden" name="feature_{$available_feature.id_feature}_value" value="0" />
			<span style="font-size: 10px; color: #666;">{l s='N/A'} -
			<a href="{$link->getAdminLink('AdminFeatures')}&amp;addfeature_value&id_feature={$available_feature.id_feature}"
			 style="color: #666; text-decoration: underline;">{l s='Add pre-defined values first'}</a>
		</span>
	{/if}
	</td>
	<td style="width:40%" class="translatable">
	{foreach from=$languages item=language}
		<div class="lang_{$language.id_lang}" style="{if $language.id_lang != $default_form_language}display:none;{/if}float: left;">
		<textarea class="custom_{$available_feature.id_feature}_" name="custom_{$available_feature.id_feature}_{$language.id_lang}" cols="40" rows="1"
			onkeyup="if (isArrowKey(event)) return ;$('#feature_{$available_feature.id_feature}_value').val(0);" >{$available_feature.val[$language.id_lang].value|htmlentitiesUTF8|default:""}</textarea>
		</div>
	{/foreach}
	</td>
</tr>

{foreachelse}
	<tr><td colspan="3" style="text-align:center;">{l s='No features defined'}</td></tr>
{/foreach}

</table>
<div class="separation"></div>
<div style="text-align:center;">
	<a href="{$link->getAdminLink('AdminFeatures')}&amp;addfeature" onclick="return confirm('{l s='You will lose all modifications not saved, you may want to save modifications first?' js=1}');">
		<img src="../img/admin/add.gif" alt="new_features" title="{l s='Add a new feature'}" />&nbsp;{l s='Add a new feature'}
	</a>
</div>

<script type="text/javascript">
	displayFlags(languages, id_language, allowEmployeeFormLang);
</script>

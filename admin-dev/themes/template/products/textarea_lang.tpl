<div class="translatable">
{foreach from=$languages item=language}
<div class="lang_{$language.id_lang}" style="{if !$language.is_default}display:none;{/if}float: left;">
	<textarea cols="100" rows="10" type="text" id="{$input_name}_{$language.id_lang}" 
	name="{$input_name}_{$language.id_lang}" 
	class="autoload_rte" >{$input_value[$language.id_lang]|htmlentitiesUTF8}</textarea>
	<span class="hint" name="help_box">{$hint|default:''}<span class="hint-pointer">&nbsp;</span></span>
</div>
{/foreach}
</div>
<script type="text/javascript">
	var iso = '{$iso_tiny_mce}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
	var ad = '{$ad}';
</script>

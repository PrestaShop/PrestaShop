{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($obj->id)}
<input type="hidden" name="submitted_tabs[]" value="Attachments" />
<legend>{l s='Attachment'}</legend>

<div class="row">
	<label class="control-label col-lg-3 required">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='Maximum 32 characters.'}">
		{l s='Filename:'}
		</span>
	</label>
	<div class="col-lg-7 translatable">
		{foreach $languages as $language}
			<div class="lang_{$language.id_lang}" style="{if $language.id_lang != $default_form_language}display:none;{/if}">
				<input type="text" name="attachment_name_{$language.id_lang}" value="{$attachment_name[$language.id_lang]|escape:'htmlall':'UTF-8'}" />
			</div>
		{/foreach}
	</div>
</div>
	
<div class="row">
	<label class="control-label col-lg-3">{l s='Description:'} </label>
	<div class="col-lg-7 translatable">
		{foreach $languages as $language}
			<div class="lang_{$language.id_lang}" style="display: {if $language.id_lang == $default_form_language}block{else}none{/if};">
				<textarea name="attachment_description_{$language.id_lang}">{$attachment_description[$language.id_lang]|escape:'htmlall':'UTF-8'}</textarea>
			</div>
		{/foreach}
	</div>
</div>


<div class="row">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Upload a file from your computer'} ({$PS_ATTACHMENT_MAXIMUM_SIZE|string_format:'%.2f'} {l s='MB max.'})">
			{l s='File:'}
		</span>
	</label>
	<div class="input-group col-lg-7">
		<input type="file" name="attachment_file" />
		<input type="submit" value="{l s='Upload attachment file'}" name="submitAddAttachments" class="btn btn-default" />
	</div>
</div>


	<table>
		<tr>
			<td>
                <p>{l s='Available attachments:'}</p>
                <select multiple id="selectAttachment2" style="width:300px;height:160px;">
                    {foreach $attach2 as $attach}
                        <option value="{$attach.id_attachment}">{$attach.name}</option>
                    {/foreach}
                </select><br /><br />
                <a href="#" id="addAttachment" style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
                    {l s='Add'} &gt;&gt;
                </a>
            </td>
            <td style="padding-left:20px;">
                <p>{l s='Attachments for this product:'}</p>
                <select multiple id="selectAttachment1" name="attachments[]" style="width:300px;height:160px;">
                    {foreach $attach1 as $attach}
                        <option value="{$attach.id_attachment}">{$attach.name}</option>
                    {/foreach}
                </select><br /><br />
                <a href="#" id="removeAttachment" style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
                    &lt;&lt; {l s='Remove'}
                </a>
			</td>
		</tr>
	</table>
	<div class="clear">&nbsp;</div>
	<input type="hidden" name="arrayAttachments" id="arrayAttachments" value="{foreach $attach1 as $attach}{$attach.id_attachment},{/foreach}" />

	<script type="text/javascript">
		var iso = '{$iso_tiny_mce}';
		var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
		var ad = '{$ad}';
	</script>
{/if}

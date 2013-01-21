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
*  @version  Release: $Revision: 14051 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/form/form.tpl"}

{block name="script"}
	$(document).ready(function() {
		if (btn_submit.length > 0)
		{
			//get reference on save and stay link
			btn_save_and_preview = $('span[class~="process-icon-save-and-preview"]').parent();

			//get reference on current save link label
			lbl_save = $('#desc-{$table}-save div');

			//submit the form
				if (btn_save_and_preview)
				{
					btn_save_and_preview.click(function() {
						//add hidden input to emulate submit button click when posting the form -> field name posted
						btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndPreview" value="1" />');
						$('#{$table}_form').submit();
					});
				}
		}
		$('#active_on').bind('click', function(){
			toggleDraftWarning(false);
		});
		$('#active_off').bind('click', function(){
			toggleDraftWarning(true);
		});		
	});
{/block}

{block name="leadin"}
	<div class="warn draft" style="{if $active}display:none{/if}">
		<p>
		<span style="float: left">
		{l s='Your CMS page will be saved as a draft'}
		</span>
		<br class="clear" />
		</p>
	</div>
{/block}

{block name="input"}
	{if $input.type == 'select_category'}
		<select name="{$input.name}">
			{$input.options.html}
		</select>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}


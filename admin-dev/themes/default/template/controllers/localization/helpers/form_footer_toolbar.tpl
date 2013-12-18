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

{if $show_toolbar}
<div class="panel-footer" id="toolbar-footer">
	<button id="desc-{$table}-import" class="btn btn-default" type="submit" name="submitLocalizationPack">
		<i class="process-icon-import" ></i> <span>{l s='Import'}</span>
	</button>
	{foreach from=$toolbar_btn item=btn key=k}
		{if $k != 'modules-list'}
			<a id="desc-{if $k=='save' || $k=='save-and-stay'}{if isset($submit_id_prefix)}{$submit_id_prefix}{else}{$table}{/if}{/if}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="btn btn-default{if $k=='save' || $k=='save-and-stay'} pull-right{/if}" href="{if isset($btn.href)}{$btn.href}{else}#{/if}" {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js}"{/if}>
				<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if} {if isset($btn.class)}{$btn.class}{/if}" ></i> <span {if isset($btn.force_desc) && $btn.force_desc == true } class="locked" {/if}>{$btn.desc}</span>
			</a>
		{/if}
	{/foreach}

	<script language="javascript" type="text/javascript">
	//<![CDATA[
		var submited = false;

		{if isset($submit_id_prefix)}
			var submit_id_prefix = '{$submit_id_prefix}';
		{else}
			var submit_id_prefix = '{$table}';
		{/if}


		//get reference on save link
		btn_save = $('#desc-'+submit_id_prefix+'-save');

		//get reference on form submit button
		btn_submit = $('#'+submit_id_prefix+'_form_submit_btn');

		if (btn_save.length > 0 && btn_submit.length > 0)
		{
			//get reference on save and stay link
			btn_save_and_stay = $('#desc-'+submit_id_prefix+'-save-and-stay');

			//get reference on current save link label
			lbl_save = $('#desc-'+submit_id_prefix+'-save');

			//override save link label with submit button value
			if (btn_submit.html().length > 0)
				lbl_save.find('span').html(btn_submit.html());

			if (btn_save_and_stay.length > 0)
			{
				//get reference on current save link label
				lbl_save_and_stay = $('#desc-'+submit_id_prefix+'-save-and-stay');

				//override save and stay link label with submit button value
				if (btn_submit.html().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked'))
					lbl_save_and_stay.find('span').html(btn_submit.html() + " {l s='and stay'} ");
			}

			//hide standard submit button
			btn_submit.hide();
			//bind enter key press to validate form
			$('#{$table}_form').find('input').keypress(function (e) {
				if (e.which == 13 && e.target.localName != 'textarea' && !$(e.target).parent().hasClass('tagify-container'))
					$('#desc-'+submit_id_prefix+'-save').click();
			});
			//submit the form
			{block name=formSubmit}
				btn_save.click(function() {
					// Avoid double click
					if (submited)
						return false;
					submited = true;

					if ($(this).attr('href').replace('#', '').replace(/\s/g, '') != '')
						return true;

					//add hidden input to emulate submit button click when posting the form -> field name posted
					btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');

					$('#{$table}_form').submit();
					return false;
				});

				if (btn_save_and_stay)
				{
					btn_save_and_stay.click(function() {
						if ($(this).attr('href').replace('#', '').replace(/\s/g, '') != '')
							return true;

						//add hidden input to emulate submit button click when posting the form -> field name posted
						btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');

						$('#{$table}_form').submit();
						return false;
					});
				}
			{/block}
		}
	//]]>
	</script>			
</div>
{/if}
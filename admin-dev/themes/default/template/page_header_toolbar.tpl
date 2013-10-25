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

<div class="page-head">
	{block name=pageTitle}
	<h2 class="page-title">
		{if isset($toolbar_btn['back'])}
			<a id="page-header-desc-{$table}-{if isset($toolbar_btn['back'].imgclass)}{$toolbar_btn['back'].imgclass}{else}{$k}{/if}" class="toolbar_btn" {if isset($toolbar_btn['back'].href)}href="{$toolbar_btn['back'].href}"{/if} title="{$toolbar_btn['back'].desc}" {if isset($toolbar_btn['back'].target) && $toolbar_btn['back'].target}target="_blank"{/if}{if isset($toolbar_btn['back'].js) && $toolbar_btn['back'].js}onclick="{$toolbar_btn['back'].js}"{/if}>
				<i class="{if isset($toolbar_btn['back'].icon)}{$toolbar_btn['back'].icon}{else}process-icon-{if isset($toolbar_btn['back'].imgclass)}{$toolbar_btn['back'].imgclass}{else}{$k}{/if}{/if} {if isset($toolbar_btn['back'].class)}{$toolbar_btn['back'].class}{/if}" ></i>
				<span {if isset($toolbar_btn['back'].force_desc) && $toolbar_btn['back'].force_desc == true } class="locked" {/if}>{$toolbar_btn['back'].desc}</span>
			</a>

		{/if}
		{if is_array($title)}{$title|end}{else}{$title}{/if}
	</h2>
	{/block}

	<div class="page-bar toolbarBox">
		<div class="btn-toolbar">
			{block name=toolbarBox}
			<ul class="cc_button nav nav-pills pull-right">
				{foreach from=$toolbar_btn item=btn key=k}
				{if $k != 'back'}
				<li>
					<a id="page-header-desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="toolbar_btn" {if isset($btn.href)}href="{$btn.href}"{/if} title="{$btn.desc}" {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js}"{/if}>
						<i class="{if isset($btn.icon)}{$btn.icon}{else}process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}{/if} {if isset($btn.class)}{$btn.class}{/if}" ></i>
						<span {if isset($btn.force_desc) && $btn.force_desc == true } class="locked" {/if}>{$btn.desc}</span>
					</a>
				</li>
				{/if}
				{/foreach}
			</ul>

			<script language="javascript" type="text/javascript">
			//<![CDATA[
				var submited = false;
				var modules_list_loaded = false;

				$(function() {
					//get reference on save link
					btn_save = $('i[class~="process-icon-save"]').parent();

					//get reference on form submit button
					btn_submit = $('#{$table}_form_submit_btn');

					if (btn_save.length > 0 && btn_submit.length > 0)
					{
						//get reference on save and stay link
						btn_save_and_stay = $('i[class~="process-icon-save-and-stay"]').parent();

						//get reference on current save link label
						lbl_save = $('#page-header-desc-{$table}-save');

						//override save link label with submit button value
						if (btn_submit.html().length > 0)
							lbl_save.find('span').html(btn_submit.html());

						if (btn_save_and_stay.length > 0) {
							//get reference on current save link label
							lbl_save_and_stay = $('#page-header-desc-{$table}-save-and-stay');

							//override save and stay link label with submit button value
							if (btn_submit.html().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked'))
								lbl_save_and_stay.find('span').html(btn_submit.html() + " {l s='and stay'} ");
						}

						//hide standard submit button
						btn_submit.hide();
						//bind enter key press to validate form
						$('#{$table}_form').find('input').keypress(function (e) {
							if (e.which == 13 && e.target.localName != 'textarea')
								$('#page-header-desc-{$table}-save').click();
						});
						//submit the form
						{block name=formSubmit}
							btn_save.click(function() {
								// Avoid double click
								if (submited)
									return false;
								submited = true;

								//add hidden input to emulate submit button click when posting the form -> field name posted
								btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');

								$('#{$table}_form').submit();
								return false;
							});

							if (btn_save_and_stay) {
								btn_save_and_stay.click(function() {
									//add hidden input to emulate submit button click when posting the form -> field name posted
									btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');
									$('#{$table}_form').submit();
									return false;
								});
							}
						{/block}
					}

					{if isset($tab_modules_open) && $tab_modules_open}
						$('#modules_list_container').modal('show');
						openModulesList();
					{/if}
				});

				{if isset($tab_modules_list)}
					$('.process-icon-modules-list').parent('a').unbind().bind('click', function (){
						$('#modules_list_container').modal('show');
						openModulesList();
					});
				{/if}
			//]]>
			</script>
			{/block}
		</div>
	</div>
</div>

{block name=pageBreadcrumb}
<ul class="breadcrumb hide">
	<li>
		{if $title}
			{foreach $title as $key => $item name=title}
				{* Use strip_tags because if the string already has been through htmlentities using escape will break it *}
				<span class="item-{$key} ">{$item|strip_tags}
					{if !$smarty.foreach.title.last}
						<img alt="&gt;" style="margin-right:5px" src="../img/admin/separator_breadcrumb.png" />
					{/if}
				</span>
			{/foreach}
		{else}
			&nbsp;
		{/if}
	</li>
</ul>
{/block}

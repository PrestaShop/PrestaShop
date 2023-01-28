{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
{if $show_toolbar}
<div class="panel-footer" id="toolbar-footer">
  {foreach from=$toolbar_btn item=btn key=k}
    {if $k != 'modules-list' && $k !='save' && $k !='save-and-stay'}
      <a
        id="desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}"
        class="btn btn-default{if isset($btn.target) && $btn.target} _blank{/if} desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}"
        href="{if isset($btn.href)}{$btn.href|escape:'html':'UTF-8'}{else}#{/if}"
        {if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}
      >
        <i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}{if isset($btn.class)} {$btn.class}{/if}"></i>
        <span {if isset($btn.force_desc) && $btn.force_desc == true } class="locked" {/if}>{$btn.desc}</span>
      </a>
    {/if}
  {/foreach}

  <div class="pull-right hidden-xs desktop-buttons">
    {if isset($toolbar_btn['save-and-stay'])}
      <a
        id="desc-{$table}-{if isset($toolbar_btn['save-and-stay'].imgclass)}{$toolbar_btn['save-and-stay'].imgclass}{else}save-and-stay{/if}"
        class="btn btn-info{if isset($toolbar_btn['save-and-stay'].target) && $toolbar_btn['save-and-stay'].target} _blank{/if} desc-{$table}-{if isset($toolbar_btn['save-and-stay'].imgclass)}{$toolbar_btn['save-and-stay'].imgclass}{else}save-and-stay{/if}"
        href="{if isset($toolbar_btn['save-and-stay'].href)}{$toolbar_btn['save-and-stay'].href|escape:'html':'UTF-8'}{else}#{/if}"
        {if isset($toolbar_btn['save-and-stay'].js) && $btn.js} onclick="{$toolbar_btn['save-and-stay'].js}"{/if}
      >
        <span {if isset($toolbar_btn['save-and-stay'].force_desc) && $toolbar_btn['save'].force_desc == true } class="locked" {/if}>{$toolbar_btn['save-and-stay'].desc}</span>
      </a>
    {/if}
    <a
      id="desc-{$table}-{if isset($toolbar_btn['save'].imgclass)}{$toolbar_btn['save'].imgclass}{else}save{/if}"
      class="btn btn-info{if isset($toolbar_btn['save'].target) && $toolbar_btn['save'].target} _blank{/if} desc-{$table}-{if isset($toolbar_btn['save'].imgclass)}{$toolbar_btn['save'].imgclass}{else}save{/if}"
      href="{if isset($toolbar_btn['save'].href)}{$toolbar_btn['save'].href|escape:'html':'UTF-8'}{else}#{/if}"
      {if isset($toolbar_btn['save'].js) && $btn.js} onclick="{$toolbar_btn['save'].js}"{/if}
    >
      <span {if isset($toolbar_btn['save'].force_desc) && $toolbar_btn['save'].force_desc == true } class="locked" {/if}>{$toolbar_btn['save'].desc}</span>
    </a>
  </div>

  <div class="btn-group dropup pull-right visible-xs mobile-buttons">
    <a
      id="desc-{$table}-{if isset($toolbar_btn['save'].imgclass)}{$toolbar_btn['save'].imgclass}{else}save{/if}"
      class="btn btn-info{if isset($toolbar_btn['save'].target) && $toolbar_btn['save'].target} _blank{/if} desc-{$table}-{if isset($toolbar_btn['save'].imgclass)}{$toolbar_btn['save'].imgclass}{else}save{/if}"
      href="{if isset($toolbar_btn['save'].href)}{$toolbar_btn['save'].href|escape:'html':'UTF-8'}{else}#{/if}"
      {if isset($toolbar_btn['save'].js) && $btn.js} onclick="{$toolbar_btn['save'].js}"{/if}
    >
      <span {if isset($toolbar_btn['save'].force_desc) && $toolbar_btn['save'].force_desc == true } class="locked" {/if}>{$toolbar_btn['save'].desc}</span>
    </a>
    {if isset($toolbar_btn['save-and-stay'])}
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <span class="caret"></span>
      <span class="sr-only">{l s='Toggle dropdown' d='Admin.Modules.Feature'}</span>
    </button>
    <ul class="dropdown-menu">
      <li>
        <a
          id="desc-{$table}-{if isset($toolbar_btn['save-and-stay'].imgclass)}{$toolbar_btn['save-and-stay'].imgclass}{else}save-and-stay{/if}"
          class="desc-{$table}-{if isset($toolbar_btn['save-and-stay'].imgclass)}{$toolbar_btn['save-and-stay'].imgclass}{else}save-and-stay{/if}"
          {if isset($toolbar_btn['save-and-stay'].target) && $toolbar_btn['save-and-stay'].target}target="_blank"{/if}
          href="{if isset($toolbar_btn['save-and-stay'].href)}{$toolbar_btn['save-and-stay'].href|escape:'html':'UTF-8'}{else}#{/if}"
        >
          <span {if isset($toolbar_btn['save-and-stay'].force_desc) && $toolbar_btn['save-and-stay'].force_desc == true } class="locked" {/if}>
              {$toolbar_btn['save-and-stay'].desc}
          </span>
        </a>
      </li>
    </ul>
    {/if}
  </div>

	<script type="text/javascript">
	//<![CDATA[
		var submited = false

		//get reference on save link
		btn_save = $('.desc-{$table}-save');

		//get reference on form submit button
		btn_submit = $('#{$table}_form_submit_btn');

		if (btn_save.length > 0 && btn_submit.length > 0) {
			// Get reference on save and stay link
			btn_save_and_stay = $('.desc-{$table}-save-and-stay');

			//get reference on current save link label
			lbl_save = $('#desc-{$table}-save');

			//override save link label with submit button value
			if (btn_submit.html().length > 0)
				lbl_save.find('span').html(btn_submit.html());

			if (btn_save_and_stay.length > 0) {
				//get reference on current save link label
				lbl_save_and_stay = $('.desc-{$table}-save-and-stay');

				//override save and stay link label with submit button value
				if (btn_submit.html().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked'))
					lbl_save_and_stay.find('span').html(btn_submit.html() + " {l s='and stay' d='Admin.Actions'} ");
			}

			//hide standard submit button
			btn_submit.hide();
			//bind enter key press to validate form
			$('#{$table}_form').find('input').keypress(function (e) {
				if (e.which == 13 && e.target.localName != 'textarea' && !$(e.target).parent().hasClass('tagify-container'))
					$('#desc-{$table}-save').click();
			});
			//submit the form
			{block name=formSubmit}
				btn_save.on('click', function() {
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

				if (btn_save_and_stay) {
					btn_save_and_stay.on('click', function() {
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

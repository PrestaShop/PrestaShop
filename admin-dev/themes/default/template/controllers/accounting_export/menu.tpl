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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	
	function validateInputDate(input, displayError)
	{
		{literal}
		dateRegex = /^\d{4}-\d{1,2}-\d{1,2}$/
  	{/literal}
			
  	if (!input.val().match(dateRegex))
  	{
			input.parent().find('span.input-error').fadeIn('fast');
			return false;
		}
		input.parent().find('span.input-error').css('display','none');
		return true;
	}
	
	function validateAccountingForm()
	{
		validation = true;
		
		$('span.input-error').css('display', 'none');
		$('.datepicker:visible').each(function() {
			if (!(validateInputDate($(this), true)))
				validation = false;
		});
    
		return validation;
	}
	
	$(document).ready(function() {
		$('#export_menu').find('a').each(function() {
			$(this).click(function() {
				blockID = 'block_' + $(this).attr('id');
				if (!$('#' + blockID).is(':visible'))
				{
				
					$('.formAccountingExport:visible').each(function() {
						$(this).fadeOut('fast', function() {
							$('#' + blockID).fadeIn('fast');
						});			
					});
				}
			});
		});
		
		$('#' + '{$defaultType}').fadeIn('fast');
	
		$('.datepicker').each(function() {
			$(this).change(function() {
				validateInputDate($(this), true);
			});
			$(this).datepicker({
	     prevText: '',
	     nextText: '',
	     dateFormat: 'yy-mm-dd'
	    });			
		});
    
    $('.formAccountingExport form input[type="submit"]').each(function()
    {
    	$(this).click(function() {
    		return validateAccountingForm();
    	});
    });
	});
</script>

{foreach from=$preventList key=name item=preventType}
	{if !empty($preventType)}
		<div class="{$name}">
			{foreach from=$preventType item=translationPrevent}
				{$translationPrevent}
			{/foreach}
		</div>
	{/if}
{/foreach}

<div id="export_menu">
	<div class="toolbarBox">
		<div class="pageTitle">
			<h3>
				<span id="current_obj" style="font-weight: normal;">{l s='Export:'}</span>
			</h3>
			{l s='Select which export you want to do:'}<br />
			{foreach from=$exportTypeList item=export}
				<a id="{$export['type']}" class="button" href="javascript:void(0);">{$export['name']}</a>
			{/foreach}
		</div>
	</div>
</div>

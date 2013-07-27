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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2013 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<div id="carrier_logo_block" style="position:absolute;top:15px;right:15px">
	<img id="carrier_logo_img" src="{if $carrier_logo}{$carrier_logo}{else}../img/404.gif{/if}" />
	<p>
		<input id="carrier_logo_input" type="file" onchange="uploadCarrierLogo();" name="carrier_logo_input" />
		<input type="hidden" id="logo" name="logo" value="" />
	</p>
	<a id="carrier_logo_remove" {if !$carrier_logo}style="display:none"{/if} href="javascript:removeCarrierLogo();"><img src="../img/admin/disabled.gif" /> {l s='Remove the logo'}</a>
	<p>
		{l s='Format:'} JPG, GIF, PNG. {l s='Filesize:'} {$max_image_size|string_format:"%.2f"} {l s='MB max.'}
		<br />{l s='Current size:'} <span id="carrier_logo_size">{l s='undefined'}</span>.
	</p>
</div>

<script type="text/javascript">
	var carrier_translation_undefined = '{l s='undefined' js='1'}';

	function removeCarrierLogo()
	{
		$('#carrier_logo_img').attr('src', '../img/404.gif');
		$('#logo').val('null');
		fixCarrierLogoDisplay();
		$('#carrier_logo_remove').hide();
	}
	
	function uploadCarrierLogo()
	{
		$.ajaxFileUpload({
			url: 'ajax-tab.php?tab=AdminCarrierWizard&token={$token|addslashes}&action=uploadLogo',
			secureuri: false,
			fileElementId: 'carrier_logo_input',
			dataType: 'xml',
			success: function (data, status) {
				data = data.getElementsByTagName('return')[0];
				var message = data.getAttribute("message");
				if (data.getAttribute("result") == "success")
				{
					$('#carrier_logo_img').attr('src', message);
					$('#logo').val(message);
					$('#carrier_logo_remove').show();
					fixCarrierLogoDisplay();
				}
				else
					alert(message);
			}
		});
	}
	
	function fixCarrierLogoDisplay()
	{
		$('<img/>').attr('src', $('#carrier_logo_img').attr('src')).load(function(){
			var maxHeight = 200;
			var maxWidth = 200;
			var res = this.width / this.height;
			$('#carrier_logo_size').text(this.width + 'x' + this.height + ' px');
			$('#carrier_logo_img').width(this.width);
			$('#carrier_logo_img').height(this.height);
			if ($('#carrier_logo_img').width() > maxWidth)
			{
				$('#carrier_logo_img').width(maxWidth);
				$('#carrier_logo_img').height(maxWidth / res);
			}
			if ($('#carrier_logo_img').height() > maxHeight)
			{
				$('#carrier_logo_img').height(maxHeight);
				$('#carrier_logo_img').width(maxHeight * res);
			}
			if ($('#logo').val() == 'null')
				$('#carrier_logo_size').text(carrier_translation_undefined);
		});
	}
	
	fixCarrierLogoDisplay();
</script>
{*
* 2007-2011 PrestaShop 
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
*  @version  Release: $Revision: 6735 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<a href="#" class="iframe" style="display:none" id="soLink"></a>
{if isset($opc) && $opc}
<script type="text/javascript">
	var opc = true;
</script>
{else}
<script type="text/javascript">
	var opc = false;
</script>
{/if}
{if isset($already_select_delivery) && $already_select_delivery}
<script type="text/javascript">
	var already_select_delivery = true;
</script>
{else}
<script type="text/javascript">
	var already_select_delivery = false;
</script>
{/if}
<script type="text/javascript">
var soInputs = new Object();
{foreach from=$inputs item=input key=name name=myLoop}
		soInputs.{$name} = "{$input|strip_tags|addslashes}";
{/foreach}

{literal}
	$('#soLink').fancybox({
			'width'				: 1000,
			'height'			: 700,
		    'autoScale'     	: false,
		    'centerOnScroll'	: true,
		    'autoDimensions'	: false,
		    'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'hideOnOverlayClick' : false,
			'hideOnContentClick' : false,
			'showCloseButton'	: true,
			'showIframeLoading' : true,
			'enableEscapeButton' : true,
			'type'				: 'iframe',
			onStart: function () {
				$('#soLink').attr('href', 'modules/socolissimo/redirect.php'+serialiseInput(soInputs));
			},
			onClosed    :   function() {
         	   $.ajax({
			       type: 'GET',
			       url: baseDir+'/modules/socolissimo/ajax.php',
			       async: false,
			       cache: false,
			       dataType : "json",
			       data: 'token={$token"}',
			       success: function(jsonData)
			       {
			       		if (jsonData.result && !opc)
			       			$('#form').submit();
			       },
			       error: function(XMLHttpRequest, textStatus, errorThrown)
				   {
				   		alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
				   }
			   });
        	}
				});
		$(document).ready(function() 
		{	
			var interval;	
			$('input[name=id_carrier]').change(function() {
				so_click();	
			});
			so_click();	
		});
		
	
	function so_click() 
	{
		if (opc)
		{
			if (!already_select_delivery)
				interval = setInterval(function()
					{
						modifyCarrierLine(false);
					},10);
					
			else if (!$('#edit_socolissimo').length)
				interval = setInterval(function()
					{
						modifyCarrierLine(true);
					},10);
		}
		else if ($('#id_carrier{/literal}{$id_carrier}{literal}').is(':not(:checked)'))
		{
			$('[name=processCarrier]').unbind('click').click(function () {
				return true;
			});
		}
		else
		{
			$('[name=processCarrier]').unbind('click').click(function () {
				if (acceptCGV())				
					$("#soLink").trigger("click");
				return false;
			})
		}
	}

function modifyCarrierLine(edit)
{
	if ($('#button_socolissimo').length != 0)
	{
		clearInterval(interval);
		// delete interval value
		interval = null;
	}
	
	$('#button_socolissimo').remove();
	if (edit && $('input[name=id_carrier]:checked').attr('value') == {/literal}{$id_carrier}{literal})
		$('#id_carrier{/literal}{$id_carrier}{literal}').parent().prepend('<a style="margin-left:5px;" class="button" id="button_socolissimo" href="#" onclick="redirect();return;" >{/literal}{$edit_label}{literal}</a>');
	else
		$('#id_carrier{/literal}{$id_carrier}{literal}').parent().prepend('<a style="margin-left:5px;" class="exclusive" id="button_socolissimo" href="#" onclick="redirect();return;" >{/literal}{$select_label}{literal}</a>');
		
	if (already_select_delivery)
	{
		$('#id_carrier{/literal}{$id_carrier}{literal}').css('display', 'block');
		$('#id_carrier{/literal}{$id_carrier}{literal}').css('margin', 'auto');
		$('#id_carrier{/literal}{$id_carrier}{literal}').css('margin-top', '5px');
	}
	else
		$('#id_carrier{/literal}{$id_carrier}{literal}').css('display', 'none');	
	
}

function redirect()
{
	document.location.href = '{/literal}{$urlSo}{literal}'+serialiseInput(soInputs);
}


function serialiseInput(inputs)
{
	updateGiftData();
	soInputs.TRPARAMPLUS = soInputs.carrier_id+'|'+soInputs.gift+'|'+soInputs.gift_message;
	str = '?firstcall=1&';
	for ( var cle in inputs )
   		str += cle+'='+inputs[cle]+'&';
   	
	return str;
}

function updateGiftData()
{
	soInputs.gift = ($('#gift').attr('checked') ? '1' : '0' );
	soInputs.gift_message = $('#gift_message').attr('value');
}

{/literal}
</script>
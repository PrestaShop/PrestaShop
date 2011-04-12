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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<a href="modules/socolissimo/redirect.php?{$serialsInput|escape:'htmlall':'UTF-8'}" class="iframe" style="display:none" id="soLink"></a>


<script type="text/javascript">
var post = '';

{foreach from=$inputs item=input key=name name=myLoop}
		post += "{$name|escape:'htmlall':'UTF-8'}={$input|strip_tags|addslashes}&"
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
			'showCloseButton'	: false,
			'showIframeLoading' : true,
			'enableEscapeButton' : false,
			'type'				: 'iframe',
			onClosed    :   function() {
         	   $.ajax({
			       type: 'GET',
			       url: baseDir+'/modules/socolissimo/ajax.php',
			       async: false,
			       cache: false,
			       dataType : "json",
			       data: 'ajax=true',
			       success: function(jsonData)
			       {
			       		if (jsonData.result)
			       			$('#form').submit();
			       		
			       },
			       error: function(XMLHttpRequest, textStatus, errorThrown)
				   {
				   		alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
				   }
			   });
        	}
			});

	function so_click() 
	{
		
		if ($('#id_carrier{/literal}{$id_carrier}{literal}').is(':not(:checked)'))
			$('[name=processCarrier]').unbind('click').click( function () { 
			 	return true;
			});
		else
			$('[name=processCarrier]').unbind('click').click(function () {
				if (acceptCGV())				
					$("#soLink").trigger("click");
				return false;
			})
	}

	$(document).ready(function() 
	{

		$('input[name=id_carrier]').change(function() {
			so_click();	
		});
		so_click();
	});
{/literal}
</script>
{foreach from=$inputs item=input key=name name=myLoop}
		<input type="hidden" name="{$name|escape:'htmlall':'UTF-8'}" value="{$input|strip_tags|addslashes}"/>
{/foreach}

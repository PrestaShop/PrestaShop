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
<script type="text/javascript">
{literal}
	function change_action_form() 
	{
		if ($('#id_carrier{/literal}{$id_carrier}{literal}').is(':not(:checked)'))
			$('#form').attr("action", 'order.php');
		else
			$('#form').attr("action", '{/literal}{$urlSo}{literal}');
	}
	$(document).ready(function() 
	{
		$('input[name=id_carrier]').change(function() {
			change_action_form();	
		});
		change_action_form();
	});
{/literal}
</script>
{foreach from=$inputs item=input key=name name=myLoop}
	<input type="hidden" name="{$name|escape:'htmlall':'UTF-8'}" value="{$input|strip_tags|addslashes}"/>
{/foreach}

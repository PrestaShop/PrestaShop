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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<html>
<head>
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
		<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}
</head>
<body>
	<script type="text/javascript">
	{if $customization_errors}
		parent.customization_errors = true;
	{else}
		parent.customization_errors = false;
		parent.$('#products_err', window.parent.document).hide();
	{/if}
	var id_selected_product = parent.$('#id_product option:selected').val();
	if (parent.searchProducts())
	{
		parent.$('#products_err', window.parent.document).html('{$customization_errors}');
		parent.$('#id_product option[value="'+id_selected_product+'"]').attr('selected', true);
		parent.$('#id_product').change();
	}
	</script>
	</body>
</html>

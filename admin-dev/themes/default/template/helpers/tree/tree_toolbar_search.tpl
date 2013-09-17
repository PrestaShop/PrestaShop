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

<!-- <label for="node-search">{l s=$label}</label> -->
<span class="input-group">
	<span class="input-group-addon">
		<i class="icon-search"></i>
	</span>
	<input type="text"
		{if isset($id)}id="{$id}"{/if}
		{if isset($name)}name="{$name}"{/if}
		class="form-control{if isset($class)} {$class}{/if}"
	/>
</span>

{if isset($typeahead_source) && isset($id)}

<script type="text/javascript">
	$(document).ready(
		function()
		{
			$("#{$id}").typeahead(
			{
				name: "{$name}",
				valueKey: 'name',
				local: [{$typeahead_source}]
			});
		}
	);
</script>
{/if}
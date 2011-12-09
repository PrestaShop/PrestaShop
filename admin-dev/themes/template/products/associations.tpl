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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="Associations">
	<h4>{l s='Product price'}</h4>
	<div class="separation"></div>
	<label for="id_category_default" class="t">
	{l s='Default category:'}
	</label>
	<div id="no_default_category" style="color: red;font-weight: bold;display: none;">
		{l s='Please check a category in order to select the default category.'}
	</div>
		{*<script type="text/javascript">
			var post_selected_cat;
			post_selected_cat = '{$selected_cat_ids}';
		</script>*}
		<select id="id_category_default" name="id_category_default">
		{foreach from=$selected_cat item=cat}
			<option value="{$cat.id_category}" {if $product->id_category_default == $cat.id_category}selected="selected"{/if} >{$cat.name}</option>
		{/foreach}
		</select>
	<div id="category_block">{$category_tree}</div>
</div>
<script type="text/javascript">
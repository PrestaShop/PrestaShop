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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}

<li class="tree-item">
	<label class="tree-item-name">
		<i class="tree-dot"></i>
		<a href="{$url_shop|escape:'html':'UTF-8'}&amp;shop_id={$node['id_shop']}">{$node['name']}</a>
	</label>
	{if isset($node['urls'])}
		<ul class="tree">
			{foreach $node['urls'] as $url}
			<li class="tree-item">
				<label class="tree-item-name">
					<i class="tree-dot"></i>
					<a href="{$url_shop_url|escape:'html':'UTF-8'}&amp;id_shop_url={$url['id_shop_url']}">{$url['name']}</a>
				</label>
			</li>
			{/foreach}
		</ul>
	{/if}
</li>

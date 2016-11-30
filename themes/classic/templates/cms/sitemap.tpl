{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Sitemap' d='Shop.Theme'}
{/block}

{block name='page_content_container'}
  <div id="sitemap-tree" class="sitemap">
    <div class="tree-top">
      <a href="{$urls.base_url}" title="{$shop.name}"></a>
    </div>
    <ul class="tree">
      {foreach $sitemap as $item}
        {if isset($item.children)}
          {foreach $item.children as $child}
            {include file='cms/_partials/sitemap-tree-branch.tpl' node=$child}
          {/foreach}
        {/if}
      {/foreach}
    </ul>
  </div>
{/block}

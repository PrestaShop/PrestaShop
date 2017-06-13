{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div id="js-product-list">
  <div class="products row">
    {foreach from=$listing.products item="product"}
      {block name='product_miniature'}
        {include file='catalog/_partials/miniatures/product.tpl' product=$product}
      {/block}
    {/foreach}
  </div>

  {block name='pagination'}
    {include file='_partials/pagination.tpl' pagination=$listing.pagination}
  {/block}

  <div class="hidden-md-up text-xsright up">
    <a href="#header" class="btn btn-secondary">
      {l s='Back to top' d='Shop.Theme.Actions'}
      <i class="material-icons">&#xE316;</i>
    </a>
  </div>
</div>

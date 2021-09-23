{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{if $product.availability == 'available' || $product.availability == 'instock'}
  {assign var="icon" value="check_circle"}
  {assign var="type" value="success"}
{else if $product.availability == 'unavailable'}
  {assign var="icon" value="error_circle"}
  {assign var="type" value="error"}
{else if $product.availability == 'last_remaining_items'}
  {assign var="icon" value="warning"}
  {assign var="type" value="warning"}
{/if}

{if $product.is_virtual	== 0 && isset($showDeliveryTime) && $showDeliveryTime}
  {if $product.additional_delivery_times == 1}
    {if $product.delivery_information}
      {assign var="deliveryInformations" value=$product.delivery_information}
    {/if}
  {elseif $product.additional_delivery_times == 2}
    {if $product.quantity > 0}
      {assign var="deliveryInformations" value=$product.delivery_in_stock}
    {* Out of stock message should not be displayed if customer can't order the product. *}
    {elseif $product.quantity <= 0 && $product.add_to_cart_url}
      {assign var="deliveryInformations" value=$product.delivery_out_stock}
    {/if}
  {/if}
{/if}


{block name='product_availability'}
  {if $product.availability_message || isset($deliveryInformations)}
    <div class="product-availability product-availability--{$type}">
      <i class="material-icons">
        {$icon}
      </i>

      <span class="product-availability-label">
        {if 
          isset($deliveryInformations) 
          && $product.availability_message
        }
          {l
          s='%availabilityMessage% - %deliveryInformations%'
          d='Shop.Theme.Checkout'
          sprintf=[
            '%deliveryInformations%' => $deliveryInformations,
            '%availabilityMessage%' => $product.availability_message
            ]
          }
        {elseif isset($deliveryInformations)}
          {$deliveryInformations}
        {else}
          {$product.availability_message}
        {/if}
      </span>

      {if isset($product.availability_submessage)}
        <p class="product-availability-options">
          {$product.availability_submessage}
        </p>
      {/if}
    </div>  
  {/if}
{/block}

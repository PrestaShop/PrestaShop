{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file=$layout}

{block name='head' append}
  <meta property="og:type" content="product">
  <meta content="{$product.url}">

  {if $product.cover}
    <meta property="og:image" content="{$product.cover.large.url}">
  {/if}

  {if $product.show_price}
    <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
    <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
    <meta property="product:price:amount" content="{$product.price_amount}">
    <meta property="product:price:currency" content="{$currency.iso_code}">
  {/if}
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}">
  <meta property="product:weight:units" content="{$product.weight_unit}">
  {/if}
{/block}

{block name='head_microdata_special'}
  {include file='_partials/microdata/product-jsonld.tpl'}
{/block}

{block name='content'}
  {* FIRST PART - PHOTO, NAME, PRICES, ADD TO CART*}
  <div class="row g-4 g-xl-5 product js-product-container">
    <div class="product__left col-lg-6 col-xl-7">
      {block name='product_cover_thumbnails'}
        {include file='catalog/_partials/product-cover-thumbnails.tpl'}
      {/block}
    </div>

    <div class="product__col col-lg-6 col-xl-5">
      {block name='product_header'}
        <h1 class="h4 product__name">{block name='page_title'}{$product.name}{/block}</h1>
      {/block}

      {block name='product_prices'}
        {include file='catalog/_partials/product-prices.tpl'}
      {/block}

      {block name='product_description_short'}
        <div class="product__description-short rich-text">{$product.description_short nofilter}</div>
      {/block}

      {block name='product_customization'}
        {if $product.is_customizable && count($product.customizations.fields)}
          {include file='catalog/_partials/product-customization.tpl' customizations=$product.customizations}
        {/if}
      {/block}

      <div class="product__actions js-product-actions">
        {block name='product_buy'}
          <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
            <input type="hidden" name="token" value="{$static_token}">
            <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
            <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" class="js-product-customization-id">

            {block name='product_variants'}
              {include file='catalog/_partials/product-variants.tpl'}
            {/block}

            {block name='product_pack'}
              {if $packItems}
                {include file='catalog/_partials/product-pack.tpl'}
              {/if}
            {/block}

            {block name='product_discounts'}
              {include file='catalog/_partials/product-discounts.tpl'}
            {/block}

            {block name='product_add_to_cart'}
              {include file='catalog/_partials/product-add-to-cart.tpl'}
            {/block}

            {block name='product_additional_info'}
              {include file='catalog/_partials/product-additional-info.tpl'}
            {/block}

            {block name='product_out_of_stock'}
              {hook h='actionProductOutOfStock' product=$product}
            {/block}

            {* Input to refresh product HTML removed, block kept for compatibility with themes *}
            {block name='product_refresh'}{/block}
          </form>
        {/block}
      </div>{* /product-actions *}
    </div>{* /col *}
  </div>{* /row *}
  {* END OF FIRST PART *}

  {* SECOND PART - REASSURANCE, TABS *}
  <div class="row">
    <div class="col-lg-6 col-xl-5 order-lg-1">
      {block name='hook_display_reassurance'}
        {hook h='displayReassurance'}
      {/block}
    </div>

    <div class="col-lg-6 col-xl-7">
      {block name='product_tabs'}
          <div class="product__infos accordion accordion-flush" id="product-infos-accordion">

            {block name='product_description'}
              {if $product.description}
                <div class="info accordion-item" id="description">
                  <h2 class="info__title accordion-header" id="product-description-heading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#product-description-collapse" aria-expanded="true" aria-controls="product-description-collapse">
                      {l s='Description' d='Shop.Theme.Catalog'}
                    </button>
                  </h2>
                  <div id="product-description-collapse" class="info__content accordion-collapse collapse show" data-bs-parent="#product-infos-accordion" aria-labelledby="product-description-heading">
                    <div class="product__description accordion-body rich-text">
                      {$product.description nofilter}
                    </div>
                  </div>
                </div>
              {/if}
            {/block}

            {block name='product_details'}
              {include file='catalog/_partials/product-details.tpl'}
            {/block}

            {block name='product_attachments'}
              {if $product.attachments}
                <div class="info accordion-item" id="attachments">
                  <h2 class="info__title accordion-header" id="product-attachments-heading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#product-attachments-collapse" aria-expanded="true" aria-controls="product-attachments-collapse">
                      {l s='Download' d='Shop.Theme.Actions'}
                    </button>
                  </h2>
                  <div id="product-attachments-collapse" class="info__content accordion-collapse collapse" data-bs-parent="#product-infos-accordion" aria-labelledby="product-attachments-heading">
                    <div class="product__attachments accordion-body">
                      {foreach from=$product.attachments item=attachment}
                        <div class="attachment">
                          <p class="h5"><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></p>
                          <p>{$attachment.description}</p>
                          <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                            {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                          </a>
                        </div>
                      {/foreach}
                    </div>
                  </div>
                </div>
              {/if}
            {/block}

            {* New collapses for module hooked content *}
            {foreach from=$product.extraContent item=extra key=extraKey}
              <div class="info accordion-item" id="extra-{$extraKey}" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
                <h2 class="info__title accordion-header" id="product-extra{$extraKey}-heading">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#product-extra{$extraKey}-collapse" aria-expanded="true" aria-controls="product-extra{$extraKey}-collapse">
                    {$extra.title}
                  </button>
                </h2>
                <div id="product-extra{$extraKey}-collapse" class="info__content accordion-collapse collapse" data-bs-parent="#product-infos-accordion" aria-labelledby="product-extra{$extraKey}-heading">
                  <div class="accordion-body">
                    {$extra.content nofilter}
                  </div>
                </div>
              </div>
            {/foreach}

          </div>
      {/block}
    </div>{* /col *}
  </div>{* /row *}
  {* END OF SECOND PART *}

  {block name='product_accessories'}
    {if $accessories}
      {include file='catalog/_partials/product-accessories.tpl'}
    {/if}
  {/block}

  {block name='product_footer'}
    {hook h='displayFooterProduct' product=$product category=$category}
  {/block}

  {block name='page_footer_container'}
    {block name='page_footer'}
    {/block}
  {/block}
{/block}

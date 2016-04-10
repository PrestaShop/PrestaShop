{extends file=$layout}

{block name='head_seo' prepend}
  <link rel="canonical" href="{$product.canonical_url}">
{/block}

{block name='head' append}
  <meta property="og:type" content="product">
  <meta property="og:url" content="{$urls.current_url}">
  <meta property="og:title" content="{$page.title}">
  <meta property="og:site_name" content="{$shop.name}">
  <meta property="og:description" content="{$page.description}">
  <meta property="og:image" content="{$product.cover.large.url}">
  <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
  <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
  <meta property="product:price:amount" content="{$product.price_amount}">
  <meta property="product:price:currency" content="{$currency.iso_code}">
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}">
  <meta property="product:weight:units" content="{$product.weight_unit}">
  {/if}
{/block}

{block name='content'}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$product.url}">

    <div class="row">
      <div class="col-md-6">
        {block name='page_content_container'}
          <section class="page-content" id="content">
            {block name='page_content'}
              {block name='product_labels'}
                <ul class="product-labels">
                  {foreach from=$product.labels item=label}
                    <li class="product-label">{$label.label}</li>
                  {/foreach}
                </ul>
              {/block}

              {block name='product_cover_tumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}

            {/block}
          </section>
        {/block}
        </div>
        <div class="col-md-6">
          {block name='page_header_container'}
            {block name='page_header'}
              <h1 class="h1" itemprop="name">{block name='page_title'}{$product.name}{/block}</h1>
            {/block}
          {/block}
          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}

          <div class="product-information">
            {block name='product_description_short'}
              <div id="product-description-short-{$product.id}" itemprop="description">{$product.description_short nofilter}</div>
            {/block}

            {if $product.is_customizable && count($product.customizations.fields)}
              {block name='product_customization'}
                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
              {/block}
            {/if}

            <div class="product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" />

                  {block name='product_variants'}
                    {include file='catalog/_partials/product-variants.tpl'}
                  {/block}

                  {block name='product_pack'}
                    {if $packItems}
                      <section class="product-pack">
                        <h3 class="h4">{l s='This pack contains'}</h3>
                        {foreach from=$packItems item="product_pack"}
                          {block name='product_miniature'}
                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                          {/block}
                        {/foreach}
                    </section>
                    {/if}
                  {/block}

                  {block name='product_discounts'}
                    {if $product.quantity_discounts}
                      <section class="product-discounts">
                        <h3 class="h6 product-discounts-title">{l s='Volume discounts'}</h3>
                        <table class="table-product-discounts">
                          <thead>
                            <tr>
                              <th>{l s='Quantity'}</th>
                              <th>{if $display_discount_price}{l s='Price'}{else}{l s='Discount'}{/if}</th>
                              <th>{l s='You Save'}</th>
                            </tr>
                          </thead>
                          <tbody>
                            {foreach from=$product.quantity_discounts item='quantity_discount' name='quantity_discounts'}
                              <tr data-discount-type="{$quantity_discount.reduction_type}" data-discount="{$quantity_discount.real_value}" data-discount-quantity="{$quantity_discount.quantity}">
                                <td>{$quantity_discount.quantity}</td>
                                <td>{$quantity_discount.discount}</td>
                                <td>{l s='Up to %s' sprintf=$quantity_discount.save}</td>
                              </tr>
                            {/foreach}
                          </tbody>
                        </table>
                      </section>
                      <hr>
                    {/if}
                  {/block}

                  {block name='product_add_to_cart'}
                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                  {/block}

                  {block name='product_refresh'}
                    <input class="product-refresh ps-hidden-by-js" name="refresh" type="submit" value="{l s='Refresh'}">
                  {/block}
                </form>
              {/block}

            </div>

            {hook h='displayReassurance'}

            <div class="tabs">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#description">{l s='Description'}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#product-details">{l s='Product Details'}</a>
                </li>
              </ul>

              <div class="tab-content" id="tab-content">
               <div class="tab-pane fade in active" id="description">
                 {block name='product_description'}
                   <div class="product-description">{$product.description nofilter}</div>
                 {/block}
               </div>

               {block name='product_details'}
                 {include file='catalog/_partials/product-details.tpl'}
               {/block}
            </div>
          </div>
        </div>
      </div>
    </div>

    {block name='product_extra_right'}
      <div class="product-extra-right">
        {hook h='displayRightColumnProduct'}
      </div>
    {/block}

    {block name='product_extra_left'}
      <div class="product-extra-left">
        {hook h='displayLeftColumnProduct'}
      </div>
    {/block}

      {block name='product_accessories'}
        {if $accessories}
          <section class="product-accessories clearfix">
            <h3 class="h5 text-uppercase">{l s='You might also like'}</h3>
            {foreach from=$accessories item="product_accessory"}
              {block name='product_miniature'}
                {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory}
              {/block}
            {/foreach}
          </section>
        {/if}
      {/block}

      {block name='product_footer'}
        {hook h='displayFooterProduct' product=$product category=$category}
      {/block}

      {block name='product_attachments'}
        {if $product.attachments}
          <section class="product-attachments">
            <h3 class="h5 text-uppercase">{l s='Download'}</h3>
            {foreach from=$product.attachments item=attachment}
              <div class="attachment">
                <h4><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">{$attachment.name}</a></h4>
                <p>{$attachment.description}</p>
                <a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">
                  {l s='Download'} ({Tools::formatBytes($attachment.file_size, 2)})
                </a>
              </div>
            {/foreach}
          </section>
        {/if}
      {/block}

    <div class="modal fade" id="product-modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
            <figure>
              <img class="js-product-cover product-cover-modal" width="{$product.cover.large.width}" src="{$product.cover.large.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" itemprop="image">
              <figcaption class="image-caption">
              {block name='product_description_short'}
                <div id="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
              {/block}
            </figcaption>
            </figure>
            <aside id="thumbnails" class="thumbnails js-thumbnails text-xs-center">
              {block name='product_images'}
                <div class="js-mask mask">
                  <ul class="product-images js-product-images">
                    {foreach from=$product.images item=image}
                      <li class="thumb-container">
                        <img data-image-large-src="{$image.large.url}" class="thumb js-thumb" src="{$image.medium.url}" alt="{$image.legend}" title="{$image.legend}" width="{$image.medium.width}" itemprop="image">
                      </li>
                    {/foreach}
                  </ul>
                </div>
              {/block}
              <div class="arrows js-arrows">
                <i class="material-icons arrow-up js-arrow-up">&#xE5C7;</i>
                <i class="material-icons arrow-down js-arrow-down">&#xE5C5;</i>
              </div>
            </aside>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    {block name='page_footer_container'}
      <footer class="page-footer">
        {block name='page_footer'}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}
  </section>

{/block}

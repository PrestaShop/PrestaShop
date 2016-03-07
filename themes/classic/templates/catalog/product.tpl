{extends file=$layout}

{block name='head_seo' prepend}
  <link rel="canonical" href="{$product.canonical_url}" />
{/block}

{block name='head' append}
  <meta property="og:type" content="product" />
  <meta property="og:url" content="{$urls.current_url}" />
  <meta property="og:title" content="{$page.title}" />
  <meta property="og:site_name" content="{$shop.name}" />
  <meta property="og:description" content="{$page.description}" />
  <meta property="og:image" content="{$product.cover.large.url}" />
  <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}" />
  <meta property="product:pretax_price:currency" content="{$currency.iso_code}" />
  <meta property="product:price:amount" content="{$product.price_amount}" />
  <meta property="product:price:currency" content="{$currency.iso_code}" />
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}" />
  <meta property="product:weight:units" content="{$product.weight_unit}" />
  {/if}
{/block}

{block name='content'}

  <section id="main" class="_gray-darker" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$product.url}">

    {block name='product_activation'}
      {include file='catalog/_partials/product-activation.tpl'}
    {/block}
    <div class="row">
      <div class="col-md-6">
        {block name='page_content_container'}
          <section id="content" class="page-content">
            {block name='page_content'}
              {block name='product_labels'}
                <ul class="product-labels">
                  {foreach from=$product.labels item=label}
                    <li class="product-label">{$label.label}</li>
                  {/foreach}
                </ul>
              {/block}

              <div class="images-container">
                {block name='product_cover'}
                  <div class="product-cover _relative">
                    <img class="js-product-cover" src="{$product.cover.large.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" width="452" itemprop="image" />
                    <div class="layer" data-toggle="modal" data-target="#product-modal">
                      <i class="material-icons zoom-in">&#xE8FF;</i>
                    </div>
                  </div>
                {/block}

                {block name='product_images'}
                  <ul class="product-images">
                    {foreach from=$product.images item=image}
                      <li class="thumb-container">
                        <img data-image-large-src = "{$image.large.url}" class="thumb js-thumb" src="{$image.medium.url}" alt="{$image.legend}" title="{$image.legend}" width="100" itemprop="image" />
                      </li>
                    {/foreach}
                  </ul>
                {/block}
              </div>

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
            {if $product.show_price}
              <div class="product-prices">
                {block name='product_discount'}
                  {if $product.has_discount}
                    <p class="product-discount">
                      <span class="regular-price">{$product.regular_price}</span>
                      {hook h='displayProductPriceBlock' product=$product type="old_price"}
                    </p>
                  {/if}
                {/block}

                {block name='product_price'}
                  <p class="product-price h5 text-uppercase {if $product.has_discount}has-discount{/if}" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <link itemprop="availability" href="https://schema.org/InStock"/>
                    <span itemprop="price" content="{$productPrice}">{$product.price}</span>
                    {if $display_taxes_label}
                     <small class="text-capitalize">{if $priceDisplay} {l s='tax excl.'}{else} {l s='Tax incl.'}{/if}</small>
                    {/if}
                    <meta itemprop="priceCurrency" content="{$currency.iso_code}" />
                    {hook h='displayProductPriceBlock' product=$product type="price"}
                    {if $product.has_discount}
                      {if $product.discount_type === 'percentage'}
                        <span class="discount-percentage">{l s='SAVE %s' sprintf=$product.discount_percentage}</span>
                      {/if}
                    {/if}
                  </p>
                {/block}

                {block name='product_without_taxes'}
                  {if $priceDisplay == 2}
                    <p class="product-without-taxes">{l s='%s tax excl.' sprintf=$product.price_tax_exc}</p>
                  {/if}
                {/block}

                {block name='product_pack_price'}
                  {if $displayPackPrice}
                    <p class="product-pack-price">{l s='Instead of %s' sprintf=$noPackPrice}</span></p>
                  {/if}
                {/block}

                {block name='product_ecotax'}
                  {if $displayEcotax}
                    <p class="price-ecotax">{l s='Including %s for ecotax' sprintf=$ecotax}
                      {if $product.has_discount}
                        {l s='(not impacted by the discount)'}
                      {/if}
                    </p>
                  {/if}
                {/block}

                {block name='product_unit_price'}
                  {if $displayUnitPrice}
                    <p class="product-unit-price">{convertPrice price=$unit_price} {l s='per %s' sprintf=$product.unity}</p>
                    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                  {/if}
                {/block}

                {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}
                {hook h='displayProductPriceBlock' product=$product type="after_price"}
              </div>
            {/if}
          {/block}

          <div class="product-information">
            {block name='product_description_short'}
              <div id="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
            {/block}
            {block name='product_customization'}
              {if $product.is_customizable}
                <section class="product-customization card card-block">
                  <h3 class="h4 card-title">{l s='Product customization'}</h3>
                  {l s='Don\'t forget to save your customization to be able to add to cart'}
                  <form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data">
                    <ul class="clearfix">
                      {foreach from=$product.customizations.fields item="field"}
                        <li class="product-customization-item">
                          <label> {$field.label}</label>
                            {if $field.type == 'text'}
                              <label>{$field.text}</label>
                              <textarea placeholder="{l s='Your message here'}" class="product-message" maxlength="250" type="text" {if $field.required} required {/if} name="{$field.input_name}"></textarea>
                              <small class="pull-xs-right">{l s='250 char. max'}</small>
                            {elseif $field.type == 'image'}
                            {if $field.is_customized}
                              <br>
                              <img src="{$field.image.small.url}">
                              <a class="remove-image" href="{$field.remove_image_url}" rel="nofollow">{l s='Remove Image'}</a>
                            {/if}
                            <span class="custom-file">
                              <span class="js-file-name">{l s='No selected file'}</span>
                              <input class="file-input js-file-input" {if $field.required} required {/if} type="file" name="{$field.input_name}" />
                              <button class="btn btn-primary">{l s='Choose file'}</button>
                            </span>
                            <small class="pull-xs-right">{l s='.png .jpg .gif'}</small>
                          {/if}
                        </li>
                      {/foreach}
                    </ul>
                    <div class="clearfix">
                      <button class="btn btn-primary pull-xs-right" type="submit" name="submitCustomizedDatas">{l s='Save Customization'}</button>
                    </div>
                  </form>
                </section>
              {/if}
            {/block}
            <div class="product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}" />
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id" />

                  {block name='product_variants'}
                    <div class="product-variants">
                      {foreach from=$groups key=id_attribute_group item=group}
                        <div class="clearfix product-variants-item">
                          <label for="group_{$id_attribute_group}">{$group.name}</label>
                          {if $group.group_type == 'select'}
                            <select data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" id="group_{$id_attribute_group}">
                              {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                <option value="{$id_attribute}" title="{$group_attribute.name}"{if $group_attribute.selected} selected="selected"{/if}>{$group_attribute.name}</option>
                              {/foreach}
                            </select>
                          {else if $group.group_type == 'color'}
                            <ul id="group_{$id_attribute_group}">
                              {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                <li class="_relative pull-xs-left">
                                  <input class="input-color" type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if} />
                                  <span class="color"
                                    {if $group_attribute.html_color_code} style="background-color: {$group_attribute.html_color_code}" {/if}
                                    {if $group_attribute.texture} style="background-image: url({$group_attribute.texture})" {/if}
                                  ><span class="sr-only">{$group_attribute.name}</span></span>
                                </li>
                              {/foreach}
                            </ul>
                          {else if $group.group_type == 'radio'}
                            <ul id="group_{$id_attribute_group}">
                              {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                <li class="_relative pull-xs-left">
                                  <input class="input-radio" type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if} />
                                  <span class="radio-label">{$group_attribute.name}</span>
                                </li>
                              {/foreach}
                            </ul>
                          {/if}
                        </div>
                      {/foreach}



                      {block name='product_pack'}
                        {if $packItems}
                          <section class="product-pack">
                            <h3 class="h4">{l s='This pack contains'}</h3>
                            {foreach from=$packItems item="product_pack"}
                              {block name='product_miniature'}
                                {include file='catalog/pack-product-miniature.tpl' product=$product_pack}
                              {/block}
                            {/foreach}
                        </section>
                        {/if}
                      {/block}

                      {block name='product_discounts'}
                        {if $quantity_discounts}
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
                                {foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
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

                          {*<form class="add-to-cart" action="{$urls.pages.cart}" method="post">*}

                            {block name='product_quantity'}
                              <p class="product-quantity _margin-top-medium">
                                <label for="quantity_wanted">{l s='Quantity'}</label>
                                <input type="text" name="qty" id="quantity_wanted" value="{$product.quantity_wanted}" class="input-group" />
                              </p>
                            {/block}

                            {block name='product_minimal_quantity'}
                              {if $product.minimal_quantity > 1}
                                <p class="product-minimal-quantity">
                                  {l s='The minimum purchase order quantity for the product is %s.' sprintf=$product.minimal_quantity}
                                </p>
                              {/if}
                            {/block}

                            <button class="btn btn-primary add-to-cart _relative" data-button-action="add-to-cart" type="submit" {if !$product.add_to_cart_url}disabled{/if}>
                              <i class="material-icons shopping-cart">&#xE547;</i>
                              {l s='Add to cart'}
                            </button>


                            {block name='product_availability'}
                             {if $product.show_availability}
                                <p id="product-availability" class="_margin-left-medium"><i class="material-icons check">&#xE5CA;</i>{$product.availability_message}</p>
                             {/if}
                            {/block}
                            {hook h='displayProductButtons' product=$product}
                          {*</form>*}

                      {/block}

                      {block name='product_refresh'}
                        <input class="product-refresh _margin-top-large ps-hidden-by-js" name="refresh" type="submit" value="{l s='Refresh'}" />
                      {/block}
                    </div>
                  {/block}
                </form>
              {/block}

            </div>

            {hook h='displayReassurance'}

            <div class="tabs _margin-top-large">
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a href="#description" class="nav-link active" data-toggle = "tab">{l s='Description'}</a>
                </li>
                <li class="nav-item">
                  <a href="#product-details" class="nav-link" data-toggle = "tab">{l s='Product Details'}</a>
                </li>
              </ul>

              <div id = "tab-content" class = "tab-content">
               <div class = "tab-pane fade in active" id = "description">
                 {block name='product_description'}
                   <div class="product-description">{$product.description nofilter}</div>
                 {/block}
               </div>

               <div class = "tab-pane fade" id = "product-details">
                 {block name='product_reference'}
                   {if $product.reference}
                     <div class="product-reference">
                       <label class="label">{l s='Reference'} </label>
                       <span itemprop="sku">{$product.reference}</span>
                     </div>
                   {/if}
                 {/block}
                 {block name='product_quantities'}
                   {if $display_quantities}
                     <div class="product-quantities">
                       <label class="label">{l s='In stock'}</label>
                       <span>{$product.quantity} {$quantity_label}</span>
                    </div>
                   {/if}
                 {/block}
                 {block name='product_availability_date'}
                   {if $product.availability_date}
                     <div class="product-availability-date">
                       <label>{l s='Availability date:'} </label>
                       <span>{$product.availability_date}</span>
                     </div>
                   {/if}
                 {/block}
                 {block name='product_out_of_stock'}
                   <div class="product-out-of-stock">
                     {hook h='actionProductOutOfStock' product=$product}
                   </div>
                 {/block}


                 {block name='product_features'}
                   {if $product.features}
                     <section class="product-features">
                       <h3 class="h6">{l s='Data sheet'}</h3>
                       <dl class="data-sheet">
                         {foreach from=$product.features item=feature}
                           <dt class="name">{$feature.name}</dt>
                           <dd class="value">{$feature.value}</dd>
                         {/foreach}
                       </dl>
                     </section>
                   {/if}
                 {/block}
               </div>
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
          <section class="product-accessories clearfix _margin-top-large">
            <h3 class="h5 text-uppercase _bolder">{l s='You might also like'}</h3>
            {foreach from=$accessories item="product_accessory"}
              {block name='product_miniature'}
                {include file='catalog/product-miniature.tpl' product=$product_accessory}
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
          <section class="product-attachments _gray-darker">
            <h3 class="h5 text-uppercase _bolder">{l s='Download'}</h3>
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
              <img class="js-product-cover product-cover-modal" width="{$image.large.width}" src="{$product.cover.large.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" itemprop="image" />
              <figcaption class="image-caption">
              {block name='product_description_short'}
                <div id="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
              {/block}
            </figcaption>
            </figure>
            <aside id="thumbnails" class="thumbnails js-thumbnails text-xs-center _relative">
              {block name='product_images'}
                <div class="js-mask mask _relative">
                  <ul class="product-images js-product-images">
                    {foreach from=$product.images item=image}
                      <li class="thumb-container">
                        <img data-image-large-src="{$image.large.url}" class="thumb js-thumb" src="{$image.medium.url}" alt="{$image.legend}" title="{$image.legend}" width="{$image.medium.width}" itemprop="image" />
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

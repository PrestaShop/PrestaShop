{extends "layout.tpl"}

{block name="head_seo" prepend}
  <link rel="canonical" href="{$product.canonical_url}" />
{/block}

{block name="content"}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$product.url}">

    {block name="page_header_container"}
      <header class="page-header">
        {block name="page_header"}
          <h1 itemprop="name">{block name="page_title"}{$product.name}{/block}</h1>
        {/block}
      </header>
    {/block}

    {block name="page_content_container"}
      <section id="content" class="page-content">
        {block name="page_content"}
          {block name="product_labels"}
            <ul class="product-labels">
              {foreach from=$product.labels item=label}
                <li>{$label.label}</li>
              {/foreach}
            </ul>
          {/block}

          {block name="product_cover"}
            <div class="product-cover">
                <img src="{$product.cover.medium.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" width="{$product.cover.medium.width}" height="{$product.cover.medium.height}" itemprop="image" />
            </div>
          {/block}

          {block name="product_images"}
            <ul class="product-images">
              {foreach from=$product.images item=image}
                <li><img src="{$image.small.url}" alt="{$image.legend}" title="{$image.legend}" width="{$image.small.width}" height="{$image.small.height}" itemprop="image" /></li>
              {/foreach}
            </ul>
          {/block}

          {block name="product_reference"}
            {if $product.reference}
              <p id="product-reference">
                <label>{l s='Reference:'} </label>
                <span itemprop="sku">{$product.reference}</span>
              </p>
            {/if}
          {/block}

          {block name="product_condition"}
            {if $product.condition}
              <p id="product-condition">
                <label>{l s='Condition:'} </label>
                <link itemprop="itemCondition" href="{$product_conditions.{$product.condition}.schema_url}"/>
                <span>{$product_conditions.{$product.condition}.label}</span>
              </p>
            {/if}
          {/block}

          {block name="product_description_short"}
            <div id="product-description-short" itemprop="description">{$product.description_short}</div>
          {/block}

          {block name="product_description"}
            <div id="product-description">{$product.description}</div>
          {/block}

          {block name="product_quantities"}
            {if $display_quantities}
              <p id="product-quantities">{$product.quantity} {$quantity_label}</p>
            {/if}
          {/block}

          {block name="product_availability"}
            {if $product.show_availability}
              <p id="product-availability">{$product.availability_message}</p>
            {/if}
          {/block}

          {block name="product_availability_date"}
            {if $product.availability_date}
              <p id="product-availability-date">
                <label>{l s='Availability date:'} </label>
                <span>{$product.availability_date}</span>
              </p>
            {/if}
          {/block}

          {block name="product_out_of_stock"}
            <div class="product-out-of-stock">
              {hook h="actionProductOutOfStock" product=$product}
            </div>
          {/block}

          {block name="product_extra_right"}
            <div class="product-extra-right">
              {hook h='displayRightColumnProduct'}
            </div>
          {/block}

          {* StarterTheme: Content Only *}
          {block name="product_extra_left"}
            <div class="product-extra-left">
              {hook h='displayLeftColumnProduct'}
            </div>
          {/block}
          {* StarterTheme: Content Only End *}

          {block name="product_buy"}
            <form action="{$product.canonical_url}" method="post">
              <input type="hidden" name="token" value="{$static_token}" />
              <input type="hidden" name="refresh_product" value="1" />

              {block name="product_prices"}
                {if $product.show_price}
                  <div class="product-prices">
                    {block name="product_price"}
                      <p class="product-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                        <link itemprop="availability" href="https://schema.org/InStock"/>
                        <span itemprop="price" content="{$productPrice}">{$product.price}</span>
                        {if $display_taxes_label}
                         {if $priceDisplay} {l s='tax excl.'}{else} {l s='tax incl.'}{/if}
                        {/if}
                        <meta itemprop="priceCurrency" content="{$currency.iso_code}" />
                        {hook h="displayProductPriceBlock" product=$product type="price"}
                      </p>
                    {/block}

                    {block name="product_discount"}
                      {if $product.has_discount}
                        <p class="product-discount">
                          <span class="regular-price">{$product.regular_price}</span>
                          {if $product.discount_type === 'percentage'}
                            <span class="discount-percentage">{$product.discount_percentage}</span>
                          {/if}
                          {hook h="displayProductPriceBlock" product=$product type="old_price"}
                        </p>
                      {/if}
                    {/block}

                    {block name="product_without_taxes"}
                      {if $priceDisplay == 2}
                        <p calss="product-without-taxes">{$product.price_tax_exc}</span> {l s='tax excl.'}</p>
                      {/if}
                    {/block}

                    {block name="product_pack_price"}
                      {if $displayPackPrice}
                        <p class="product-pack-price">{l s='Instead of %s' sprintf=$noPackPrice}</span></p>
                      {/if}
                    {/block}

                    {block name="product_ecotax"}
                      {if $displayEcotax}
                        <p class="price-ecotax">{l s='Including %s for ecotax' sprintf=$ecotax}
                          {if $product.has_discount}
                            {l s='(not impacted by the discount)'}
                          {/if}
                        </p>
                      {/if}
                    {/block}

                    {block name="product_unit_price"}
                      {if $displayUnitPrice}
                        <p class="product-unit-price">{convertPrice price=$unit_price} {l s='per %s' sprintf=$product.unity}</p>
                        {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                      {/if}
                    {/block}

                    {hook h="displayProductPriceBlock" product=$product type="weight" hook_origin='product_sheet'}
                    {hook h="displayProductPriceBlock" product=$product type="after_price"}
                  </div>
                {/if}
              {/block}

              {block name="product_details"}
                {if $product.add_to_cart_url}
                  <div class="product-details">
                    {block name="product_attributes"}
                      {foreach from=$groups key=id_attribute_group item=group}
                        <div>
                          <label for="group_{$id_attribute_group}">{$group.name}</label>
                          {if $group.group_type == 'select'}
                            <select name="group[{$id_attribute_group}]" id="group_{$id_attribute_group}">
                              {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                <option value="{$id_attribute}" title="{$group_attribute.name}"{if $group_attribute.selected} selected="selected"{/if}>{$group_attribute.name}</option>
                              {/foreach}
                            </select>
                          {else if $group.group_type == 'color'}
                            <ul id="group_{$id_attribute_group}">
                              {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                <li>
                                  <input type="radio" name="group[{$id_attribute_group}]" value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if} />
                                  <span style="background-color:{$group_attribute.html_color_code}">{$group_attribute.name}</span>
                                </li>
                              {/foreach}
                            </ul>
                          {else if $group.group_type == 'radio'}
                            <ul id="group_{$id_attribute_group}">
                              {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                <li>
                                  <input type="radio" name="group[{$id_attribute_group}]" value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if} />
                                  <span>{$group_attribute.name}</span>
                                </li>
                              {/foreach}
                            </ul>
                          {/if}
                        </div>
                      {/foreach}
                    {/block}
                  </div>
                {/if}
              {/block}

              {block name="product_refresh"}
                <input type="submit" value="{l s='Refresh'}" />
              {/block}

            </form>
          {/block}

          {block name="product_add_to_cart"}
            {if $product.add_to_cart_url}
              <form action="{$urls.pages.cart}" method="post">
                <input type="hidden" name="token" value="{$static_token}" />
                <input type="hidden" name="add" value="1" />
                <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id" />
                <input type="hidden" name="id_product_attribute" id="idCombination" value="{$product.id_product_attribute}" />

                {block name="product_quantity"}
                  <p class="product-quantity">
                    <label for="quantity_wanted">{l s='Quantity'}</label>
                    <input type="number" min="1" name="qty" id="quantity_wanted" value="{$quantityBackup}" />
                  </p>
                {/block}

                {block name="product_minimal_quantity"}
                  {if $product.minimal_quantity > 1}
                    <p class="product-minimal-quantity">
                      {l s='The minimum purchase order quantity for the product is %s.' sprintf=$product.minimal_quantity}
                    </p>
                  {/if}
                {/block}

                <input type="submit" value="{l s='Add to cart'}" />
              </form>
            {/if}
          {/block}

          {* StarterTheme: Content Only *}

          {block name="product_discounts"}
            {if $quantity_discounts}
              <section class="product-discounts">
                <h3>{l s='Volume discounts'}</h3>
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
            {/if}
          {/block}

          {block name="product_features"}
            {if $product.features}
              <section class="product-features">
                <h3>{l s='Data sheet'}</h3>
                <ul>
                  {foreach from=$product.features item=feature}
                  <li>{$feature.name} - {$feature.value}</td>
                  {/foreach}
                </ul>
              </section>
            {/if}
          {/block}

          {block name="product_pack"}
            {if $packItems}
              <section class="product-pack">
                <h3>{l s='Pack content'}</h3>
                {foreach from=$packItems item="product_pack"}
                  {block name="product_miniature"}
                    {include './product-miniature.tpl' product=$product_pack}
                  {/block}
                {/foreach}
            </section>
            {/if}
          {/block}

          {block name="product_accessories"}
            {if $accessories}
              <section class="product-accessories">
                <h3>{l s='Accessories'}</h3>
                {foreach from=$accessories item="product_accessory"}
                  {block name="product_miniature"}
                    {include './product-miniature.tpl' product=$product_accessory}
                  {/block}
                {/foreach}
              </section>
            {/if}
          {/block}

          {block name="product_footer"}
            {hook h="displayFooterProduct" product=$product category=$category}
          {/block}

          {block name="product_attachments"}
            {if $product.attachments}
              <section class="product-attachments">
                <h3>{l s='Download'}</h3>
                {foreach from=$product.attachments item=attachment}
                  <div class="attachment">
                    <h4><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">{$attachment.name}</a></h4>
                    <p>{$attachment.description}</p>
                    <a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">
                      {l s="Download"} ({Tools::formatBytes($attachment.file_size, 2)})
                    </a>
                  </div>
                {/foreach}
              </section>
            {/if}
          {/block}

          {block name="product_customization"}
            {if $product.customizable}
              <section class="product-customization">
                <h3>{l s='Product customization'}</h3>
                <form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data">
                  <p>
                    {l s='After saving your customized product, remember to add it to your cart.'}
                    {if $product.uploadable_files}
                      <br />{l s='Allowed file formats are: GIF, JPG, PNG'}
                    {/if}
                  </p>
                  {if $product.uploadable_files}
                    <div>
                      <h5>{l s='Pictures'}</h5>
                      <ul id="uploadable_files">
                        {foreach from=$customizationFields item='field' name='customizationFields'}
                          {if $field.type == 0}
                            <li class="customizationUploadLine{if $field.required} required{/if}">
                              {if isset($pictures.{$field.key})}
                                <div class="customizationUploadBrowse">
                                  <img src="{$urls.pic_url}{$pictures.{$field.key}}_small" alt="" />
                                    <a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)}" title="{l s='Delete'}" >X</a>
                                </div>
                              {/if}
                              <div class="customizationUploadBrowse">
                                <label for="img{$smarty.foreach.customizationFields.index}" class="customizationUploadBrowseDescription">
                                  {if !empty($field.name)}
                                    {$field.name}
                                  {else}
                                    {l s='Please select an image file from your computer'}
                                  {/if}
                                  {if $field.required}<sup>*</sup>{/if}
                                </label>
                                <input type="file" name="file{$field.id_customization_field}" id="img{$smarty.foreach.customizationFields.index}" class="customization_block_input {if isset($pictures.{$field.key})}filled{/if}" />
                              </div>
                            </li>
                          {/if}
                        {/foreach}
                      </ul>
                    </div>
                  {/if}
                  {if $product.text_fields}
                    <div class="customizableProductsText">
                      <h5>{l s='Text'}</h5>
                      <ul id="text_fields">
                      {foreach from=$customizationFields item='field' name='customizationFields'}
                        {if $field.type == 1}
                          <li class="customizationUploadLine{if $field.required} required{/if}">
                            <label for="textField{$smarty.foreach.customizationFields.index}">
                              {if !empty($field.name)}
                                {$field.name}
                              {/if}
                              {if $field.required}<sup>*</sup>{/if}
                            </label>
                            <textarea name="textField{$field.id_customization_field}" class="customization_block_input" id="textField{$smarty.foreach.customizationFields.index}" rows="3" cols="20">{strip}
                              {if isset($textFields.{$field.key})}
                                {$textFields.{$field.key}|stripslashes}
                              {/if}
                            {/strip}</textarea>
                          </li>
                        {/if}
                      {/foreach}
                      </ul>
                    </div>
                  {/if}
                  <p id="customizedDatas">
                    <input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
                    <input type="hidden" name="submitCustomizedDatas" value="1" />
                    <button name="saveCustomization">
                      <span>{l s='Save'}</span>
                    </button>
                  </p>
                </form>
                <p class="clear required"><sup>*</sup> {l s='required fields'}</p>
              </section>
            {/if}
          {/block}

          {* StarterTheme: Content Only End *}
        {/block}
      </section>
    {/block}

    {block name="page_footer_container"}
      <footer class="page-footer">
        {block name="page_footer"}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}

  </section>

{/block}

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

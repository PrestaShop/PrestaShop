<div class="images-container">
  {block name='product_cover'}
    <div class="product-cover">
      <img class="js-qv-product-cover" src="{$product.cover.bySize.medium_default.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" width="{$product.cover.bySize.medium_default.width}" itemprop="image">
      <div class="layer hidden-sm-down" data-toggle="modal" data-target="#product-modal">
        <i class="material-icons zoom-in">&#xE8FF;</i>
      </div>
    </div>
  {/block}

  {block name='product_images'}
    <div class="js-qv-mask mask">
      <ul class="product-images js-qv-product-images">
        {foreach from=$product.images item=image}
          <li class="thumb-container">
            <img data-image-large-src="{$image.bySize.medium_default.url}" class="thumb js-thumb" src="{$image.bySize.home_default.url}" alt="{$image.legend}" title="{$image.legend}" width="100" itemprop="image">
          </li>
        {/foreach}
      </ul>
    </div>
  {/block}
</div>

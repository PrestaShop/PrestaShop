<article>
  <div class="card">
    <div class="pack-product-container">
      <div class="thumb-mask">
        <div class="mask">
          <img
            src = "{$product.cover.medium.url}"
            alt = "{$product.cover.legend}"
            data-full-size-image-url = "{$product.cover.large.url}"
          >
        </div>
      </div>
      <div class="pack-product-name">
        {$product.name}
      </div>
      <div class="pack-product-price">
        <strong>{$product.price}</strong>
      </div>
      <div class="pack-product-quantity">
        <span>x {$product.pack_quantity}</span>
      </div>
    </div>
  </div>
</article>

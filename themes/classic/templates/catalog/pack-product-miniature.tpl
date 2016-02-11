<article>
  <div class="card">
    <h1 class="p-l-2 _margin-top-medium h5">{$product.name}</h1>
    <div class="_flexbox">
        <img
          src = "{$product.cover.medium.url}"
          alt = "{$product.cover.legend}"
          data-full-size-image-url = "{$product.cover.large.url}"
        >
        <div class="card-block">
          <div class="card-text">
            {$product.description_short nofilter}
            {$product.description nofilter}
          </div>
        </div>
    </div>
  </div>
</article>

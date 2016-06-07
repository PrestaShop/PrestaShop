{block name='brand'}
  <li class="brand">
    <div class="brand-img"><a href="{$brand.url}"><img src="{$brand.image}" alt="{$brand.name}"></a></div>
    <div class="brand-infos">
      <h3><a href="{$brand.url}">{$brand.name}</a></h3>
      {$brand.text nofilter}
    </div>
    <div class="brand-products">
      <a href="{$brand.url}">{$brand.nb_products}</a>
      <a href="{$brand.url}">{l s='View products' d='Shop.Theme.Actions'}</a>
    </div>
  </li>
{/block}

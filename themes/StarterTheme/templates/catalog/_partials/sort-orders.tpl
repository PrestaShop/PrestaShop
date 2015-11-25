<div class="products-sort-order">
  <span>{l s='Sort by:'}</span>
  {foreach from=$sort_orders item=sort_order}
    <a
      rel="nofollow"
      href="{$sort_order.url}"
      class="{['current' => $sort_order.current, 'js-search-link' => true]|classnames}"
    >
      {$sort_order.label}
    </a>
  {/foreach}
</div>

<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "itemListElement": [
        {foreach from=$listing.products item=item key="position" name=producttmp}
          {
            "@type": "ListItem",
            "position": {$position},
            "name": "{$item.name}",
            "url": "{$item.url}"
            }{if !$smarty.foreach.producttmp.last},{/if}
          {/foreach}]
        }
</script>
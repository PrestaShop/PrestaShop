
{if isset($listing.rendered_facets)}
<div id="search_filters_wrapper" class="hidden-sm-down">
  <div id="search_filter_controls" class="hidden-md-up">
      <button class="btn btn-secondary clean">Clean ALL</button>
      <button class="btn btn-secondary ok">OK</button>
  </div>
  {$listing.rendered_facets nofilter}
</div>
{/if}

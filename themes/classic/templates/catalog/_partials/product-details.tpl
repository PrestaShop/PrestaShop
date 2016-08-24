<div class="tab-pane fade{if !$product.description} in active{/if}" id="product-details">
  {block name='product_reference'}
    {if $product.reference}
      <div class="product-reference">
        <label class="label">{l s='Reference' d='Shop.Theme.Catalog'} </label>
        <span itemprop="sku">
          {foreach from=$product.attributes item=att name=reference}
            {if $smarty.foreach.reference.first}
              {if $att['reference'] != null}
                {$att['reference']}
              {else}
                {$product.reference}
              {/if}
            {/if}
          {/foreach}
        </span>
      </div>
    {/if}
    {/block}
    {block name='product_quantities'}
      {if $product.show_quantities}
        <div class="product-quantities">
          <label class="label">{l s='In stock' d='Shop.Theme.Catalog'}</label>
          <span>{$product.quantity} {$product.quantity_label}</span>
        </div>
      {/if}
    {/block}
    {block name='product_availability_date'}
      {if $product.availability_date}
        <div class="product-availability-date">
          <label>{l s='Availability date:' d='Shop.Theme.Catalog'} </label>
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
          <h3 class="h6">{l s='Data sheet' d='Shop.Theme.Catalog'}</h3>
          <dl class="data-sheet">
            {foreach from=$product.features item=feature}
              <dt class="name">{$feature.name}</dt>
              <dd class="value">{$feature.value}</dd>
            {/foreach}
      {/if}

      {foreach from=$product.attributes item=att name=reference}
        {if $smarty.foreach.reference.first}
          {foreach from=$att key=key item=a name=test}
            {if ($smarty.foreach.test.index) >= 5}
              {if null != $a}
                  <dt class="name">{l s=$key|ucfirst d='Shop.Theme.Catalog'}</dt>
                  <dd class="value">{$a}</dd>
              {elseif null != $product[$key]}
                  <dt class="name">{l s=$key|ucfirst d='Shop.Theme.Catalog'}</dt>
                  <dd class="value">{$product[$key]}</dd>
              {/if}
            {/if}
          {/foreach}
        {/if}
      {/foreach}
          </dl>
        </section>
    {/block}

    {block name='product_condition'}
      {if $product.condition}
        <div class="product-condition">
          <label class="label">{l s='Condition' d='Shop.Theme.Catalog'} </label>
          <link itemprop="itemCondition" href="{$product.condition.schema_url}"/>
          <span>{$product.condition.label}</span>
        </div>
      {/if}
    {/block}
</div>

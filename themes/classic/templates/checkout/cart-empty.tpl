{extends file=$layout}

{block name='content'}

  <section id="main" class="card text-xs-center">
    <div class="card-block"><h1 class="display-4 card-title">{l s='Oh no! Your shopping cart is empty'}</h1></div>
    <a class="btn btn-primary" href="{$allProductsLink}">{l s='Add some products'}</a>
  </section>

{/block}

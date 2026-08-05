{extends file='page.tpl'}

{block name='page_title'}
  {l s='Twój Kredyt Kupiecki' mod='advancecredit'}
{/block}

{block name='page_content'}
  <section class="card card-block">
    <div class="row text-xs-center">
      <div class="col-md-4">
        <h5>{l s='Przyznany limit' mod='advancecredit'}</h5>
        <p class="h3 font-weight-bold">{$credit_limit|number_format:2:',':' '} PLN</p>
      </div>
      <div class="col-md-4">
        <h5>{l s='Wykorzystane środki' mod='advancecredit'}</h5>
        <p class="h3 font-weight-bold text-danger">{$current_debt|number_format:2:',':' '} PLN</p>
      </div>
      <div class="col-md-4">
        <h5>{l s='Dostępny limit' mod='advancecredit'}</h5>
        <p class="h3 font-weight-bold text-success">{$available_credit|number_format:2:',':' '} PLN</p>
      </div>
    </div>
  </section>
{/block}

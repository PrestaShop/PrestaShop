<div class="card mt-3">
  <div class="card-header">
    <h3 class="card-header-title">
      <i class="material-icons">credit_card</i> {l s='Kredyt kupiecki' mod='advancecredit'}
    </h3>
  </div>
  <div class="card-body">
    <div class="row text-center">
      <div class="col-md-4">
        <p class="text-muted mb-1">{l s='Przyznany limit' mod='advancecredit'}</p>
        <h3 class="fw-bold">{$credit_limit|number_format:2:',':' '} PLN</h3>
      </div>
      <div class="col-md-4">
        <p class="text-muted mb-1">{l s='Aktualne zadłużenie' mod='advancecredit'}</p>
        <h3 class="fw-bold text-danger">{$current_debt|number_format:2:',':' '} PLN</h3>
      </div>
      <div class="col-md-4">
        <p class="text-muted mb-1">{l s='Dostępny limit' mod='advancecredit'}</p>
        <h3 class="fw-bold text-success">{$available_credit|number_format:2:',':' '} PLN</h3>
      </div>
    </div>
  </div>
</div>

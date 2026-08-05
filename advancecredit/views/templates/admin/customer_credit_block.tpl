<div class="card customer-credit-card mt-3">
  <div class="card-header">
    <h3 class="card-header-title">
      <i class="material-icons">credit_card</i> {l s='Kredyt kupiecki (AdvanceCredit)' d='Modules.Advancecredit.Admin'}
    </h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4">
        <p class="mb-1 text-muted">{l s='Przyznany limit:' d='Modules.Advancecredit.Admin'}</p>
        <h4 class="font-weight-bold">{$credit_limit|string_format:"%.2f"} PLN</h4>
      </div>
      <div class="col-md-4">
        <p class="mb-1 text-muted">{l s='Wykorzystany dług:' d='Modules.Advancecredit.Admin'}</p>
        <h4 class="font-weight-bold text-danger">{$current_debt|string_format:"%.2f"} PLN</h4>
      </div>
      <div class="col-md-4">
        <p class="mb-1 text-muted">{l s='Dostępne środki:' d='Modules.Advancecredit.Admin'}</p>
        <h4 class="font-weight-bold text-success">{$available_credit|string_format:"%.2f"} PLN</h4>
      </div>
    </div>
  </div>
</div>

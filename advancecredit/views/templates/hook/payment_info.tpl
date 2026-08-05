<div class="advancecredit-payment-info card card-block mt-2 p-3">
  <p class="mb-2">
    <strong>{l s='Płatność w ramach Kredytu Kupieckiego' mod='advancecredit'}</strong>
  </p>
  <ul class="list-unstyled mb-0 small">
    <li><strong>{l s='Przyznany limit:' mod='advancecredit'}</strong> {$credit_limit|number_format:2:',':' '} PLN</li>
    <li><strong>{l s='Obecne wykorzystanie:' mod='advancecredit'}</strong> {$current_debt|number_format:2:',':' '} PLN</li>
    <li class="text-success font-weight-bold mt-1">
      <strong>{l s='Dostępne środki:' mod='advancecredit'}</strong> {$available_credit|number_format:2:',':' '} PLN
    </li>
  </ul>
</div>

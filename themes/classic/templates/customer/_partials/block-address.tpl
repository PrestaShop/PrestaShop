<article id="address-{$address.id}" class="card address" data-id-address="{$address.id}">
  <div class="card-header">
    {$address.alias}
  </div>
  <div class="card-block">
    <address>{$address.formatted nofilter}</address>
    <footer>
      <a href="{url entity=address id=$address.id}" data-link-action="edit-address" class="btn btn-success">
        {l s='Update'}
      </a>
      <a href="{url entity=address id=$address.id params=['delete' => 1, 'token' => $token]}" data-link-action="delete-address" class="btn btn-danger">
        {l s='Delete'}
      </a>
    </footer>
  </div>
</article>

<section class="contact-form">
  <form action="#" method="post">

    <section class="form-fields">

      <div class="form-group row">
        <div class="col-md-9 col-md-offset-3">
          <h3>{l s='Send a message'}</h3>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Subject Heading'}</label>
        <div class="col-md-4">
          <select name="id_contact" class="form-control form-control-select">
            {foreach from=$contact.contacts item=contact_elt}
              <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
            {/foreach}
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Email address'}</label>
        <div class="col-md-4">
          <input type="email" name="from" value="{$contact.email}" class="form-control">
        </div>
      </div>

      {if $contact.orders}
        <div class="form-group row">
          <label class="col-md-3 form-control-label">{l s='Order reference'}</label>
          <div class="col-md-4">
            <select name="id_order" class="form-control form-control-select">
              {foreach from=$contact.orders item=order}
                <option value="{$order.id_order}">{$order.reference}</option>
              {/foreach}
            </select>
          </div>
        </div>
      {/if}

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Attach File'}</label>
        <div class="col-md-6">
          <input type="file" name="fileUpload" class="filestyle">
        </div>
        <span class="col-md-3 row form-control-comment">
          {l s='optional'}
        </span>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Message'}</label>
        <div class="col-md-9">
          <textarea rows="3" name="message" class="form-control">{if $contact.message}{$contact.message}{/if}</textarea>
        </div>
      </div>

    </section>

    <footer class="form-footer text-xs-right">
      <input class="btn btn-primary" type="submit" name="submitMessage" value="{l s='Send'}">
    </footer>

  </form>
</section>

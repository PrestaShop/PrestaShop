<h1>{l s='Customer service - Contact us'}</h1>

<section class="login-form">
  <form action="#" method="post">

    <header>
      <h3>{l s='Send a message'}</h3>
      <p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
    </header>

    <section class="form-fields">

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Subject Heading'}</label>
        <div class="col-md-9">
          <select name="id_contact" class="form-control">
            {foreach from=$contact.contacts item=contact_elt}
              <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
            {/foreach}
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Email address'}</label>
        <div class="col-md-9">
          <input type="email" name="from" value="{$contact.email}" class="form-control" />
        </div>
      </div>

      {if $contact.orders}
        <div class="form-group row">
          <label class="col-md-3 form-control-label">{l s='Order reference'}</label>
          <div class="col-md-9">
            <select name="id_order" class="form-control">
              {foreach from=$contact.orders item=order}
                <option value="{$order.id_order}">{$order.reference}</option>
              {/foreach}
            </select>
          </div>
        </div>
      {/if}

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Attach File'}</label>
        <div class="col-md-9">
          <input type="file" name="fileUpload" class="form-control-valign" />
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Message'}</label>
        <div class="col-md-9">
          <textarea rows="3" name="message" class="form-control">{if $contact.message}{$contact.message}{/if}</textarea>
        </div>
      </div>

    </section>

    <footer class="form-footer">
      <button type="submit" name="submitMessage" class="btn btn-primary">
        {l s='Send'}
      </button>
    </footer>

  </form>
</section>

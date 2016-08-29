{extends file='page.tpl'}

{block name='page_title'}
  {l s='Guest Order Tracking' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  <form id="guestOrderTrackingForm" action="{$urls.pages.guest_tracking}" method="get">
    <header>
      <p>{l s='To track your order, please enter the following information:' d='Shop.Theme.CustomerAccount'}</p>
    </header>

    <section class="form-fields">

      <div class="form-group row">
        <label class="col-md-3 form-control-label required">
          {l s='Order Reference:' d='Shop.Forms.Labels'}
        </label>
        <div class="col-md-6">
          <input
            class="form-control"
            name="order_reference"
            type="text"
            size="8"
            value="{if isset($smarty.request.order_reference)}{$smarty.request.order_reference}{/if}"
          >
          <div class="form-control-comment">
            {l s='For example: QIIXJXNUI or QIIXJXNUI#1' d='Shop.Theme.CustomerAccount'}
          </div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label required">
          {l s='Email:' d='Shop.Forms.Labels'}
        </label>
        <div class="col-md-6">
          <input
            class="form-control"
            name="email"
            type="email"
            value="{if isset($smarty.request.email)}{$smarty.request.email}{/if}"
          >
        </div>
      </div>

    </section>

    <footer class="form-footer text-xs-center clearfix">
      <button class="btn btn-primary" type="submit">
        {l s='Send' d='Shop.Theme.Actions'}
      </button>
    </footer>
  </form>
{/block}

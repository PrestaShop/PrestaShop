{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='page.tpl'}

{block name='container_class'}container container--limited-md{/block}

{block name='page_title'}
  {l s='Guest Order Tracking' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <form id="guestOrderTrackingForm" action="{$urls.pages.guest_tracking}" method="get">
    <header>
      <p>{l s='To track your order, please enter the following information:' d='Shop.Theme.Customeraccount'}</p>
    </header>

    <section class="form-fields">
      <input type="hidden" name="controller" value="guest-tracking">

      <div class="mb-3 row">
        <label class="col-md-3 form-label required">
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
          <div class="text">
            {l s='For example: QIIXJXNUI or QIIXJXNUI#1' d='Shop.Theme.Customeraccount'}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
        <label class="col-md-3 form-label required">
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

    <footer class="form-footer text-sm-center">
      <button class="btn btn-primary" type="submit">
        {l s='Send' d='Shop.Theme.Actions'}
      </button>
    </footer>
  </form>
{/block}

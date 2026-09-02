{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file=$layout}

{block name='notifications'}
{/block}

{block name='breadcrumb'}
{/block}

{block name='content_columns'}
  {include file='checkout/checkout-navigation.tpl'}

  {block name='checkout_notifications'}
    {include file='_partials/notifications.tpl'}
  {/block}

  <div class="container">
    <div class="row">

      <div class="cart-grid-body tab-content col-lg-7">
        {block name='checkout_process'}
          {render file='checkout/checkout-process.tpl' ui=$checkout_process}
        {/block}
      </div>
      <div class="cart-grid-right col-lg-5">
        <div class="accordion">
          <div class="accordion-item bg-transparent">
            <button class="accordion-button collapsed px-0 mb-3 d-flex d-lg-none bg-transparent" type="button" data-bs-target="#js-checkout-summary" data-bs-toggle="collapse" aria-expanded="false">
              {l s='Order summary' d='Shop.Theme.Checkout'}
            </button>

            {block name='cart_summary'}
              {include file='checkout/_partials/cart-summary.tpl' cart=$cart}
            {/block}
          </div>
        </div>

        <hr />

        {hook h='displayReassurance'}
      </div>
    </div>
  </div>

  {include file='checkout/_partials/modal-terms.tpl'}
{/block}

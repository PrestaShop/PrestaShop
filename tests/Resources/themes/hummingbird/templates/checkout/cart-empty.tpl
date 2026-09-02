{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='checkout/cart.tpl'}

{block name='continue_shopping' append}
  <a class="btn btn-outline-primary btn-with-icon" href="{$urls.pages.index}">
    <i class="material-icons rtl-flip" aria-hidden="true">chevron_left</i>
    {l s='Continue shopping' d='Shop.Theme.Actions'}
  </a>
{/block}

{block name='cart_actions'}
  <div class="checkout text-sm-center card-block">
    <button type="button" class="btn btn-primary w-100 disabled" disabled>{l s='Checkout' d='Shop.Theme.Actions'}</button>
  </div>
{/block}

{block name='continue_shopping'}{/block}
{block name='cart_voucher'}{/block}
{block name='display_reassurance'}{/block}

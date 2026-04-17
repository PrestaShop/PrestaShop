{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends 'customer/page.tpl'}

{block name='page_title'}
  {l s='Your addresses' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <div class="row addresses">
    {foreach $customer.addresses as $address}
      <div class="col-lg-4 col-md-6 mb-3 address__wrapper">
        {block name='customer_address'}
          {include file='customer/_partials/block-address.tpl' address=$address}
        {/block}
      </div>
    {/foreach}

    <div class="col-lg-4 col-md-6 mb-3">
      <a class="addresses__new-address" href="{$urls.pages.address}" data-link-action="add-address">
        <span class="new-address__text">{l s='Add new address' d='Shop.Theme.Actions'}</span>
        <div class="new-address__icon">
          <i class="material-icons" aria-hidden="true">&#xE145;</i>
        </div>
      </a>
    </div>
  </div>
{/block}

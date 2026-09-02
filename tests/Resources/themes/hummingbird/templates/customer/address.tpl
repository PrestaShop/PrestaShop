{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends 'customer/page.tpl'}

{block name='page_title'}
  {if $editing}
    {l s='Update your address' d='Shop.Theme.Customeraccount'}
  {else}
    {l s='New address' d='Shop.Theme.Customeraccount'}
  {/if}
{/block}

{block name='page_content'}
  <div class="address-form">
    {render template='customer/_partials/address-form.tpl' ui=$address_form}
  </div>
{/block}

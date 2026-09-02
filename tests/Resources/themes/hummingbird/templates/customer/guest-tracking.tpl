{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='customer/order-detail.tpl'}

{block name='page_title'}
  {l s='Guest Tracking' d='Shop.Theme.Customeraccount'}
{/block}

{block name='order_detail'}
  {include file='customer/_partials/order-detail-no-return.tpl'}
{/block}

{block name='order_messages'}
{/block}

{if !$registered_customer_exists}
  {block name='page_content' append}
    {block name='account_transformation_form'}
      {include file='customer/_partials/account-transformation-form.tpl'}
    {/block}
  {/block}
{/if}

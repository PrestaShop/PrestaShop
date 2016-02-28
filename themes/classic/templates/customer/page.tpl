{extends file='page.tpl'}

{block name='notifications'}{/block}

{block name='page_content_top'}
  {block name='customer_notifications'}
    {include file='_partials/notifications.tpl'}
  {/block}
{/block}

{block name='page_footer'}
  {block name='my_account_links'}
    {include file='customer/_partials/my-account-links.tpl'}
  {/block}
{/block}

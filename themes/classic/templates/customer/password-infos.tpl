{extends file='page.tpl'}

{block name='page_title'}
  {l s='Forgot your password?' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content_container'}{/block}

{block name='page_footer'}
  <ul>
    <li><a href="{$urls.pages.authentication}">{l s='Back to Login' d='Shop.Theme.Actions'}</a></li>
  </ul>
{/block}

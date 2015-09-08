{block name="header_banner"}
  {hook h='displayBanner'}
{/block}

{block name="header_nav"}
  {hook h='displayNav'}
{/block}

{block name="header_logo"}
  <a class="logo" href="{$urls.base_url}" title="{$shop_name}">
    <img src="{$logo_url}" alt="{$shop_name}" />
  </a>
{/block}

{block name="header_top"}
  {hook h='displayTop'}
{/block}

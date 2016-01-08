{block name="header_banner"}
  <div class="header-banner">
    {hook h="displayBanner"}
  </div>
{/block}

{block name="header_nav"}
  <div class="header-nav">
    {hook h="displayNav"}
  </div>
{/block}

{block name="header_logo"}
  <a class="logo" href="{$urls.base_url}" title="{$shop.name}">
    <img src="{$shop.logo}" alt="{$shop.name}" />
  </a>
{/block}

{block name="header_top"}
  <div class="header-top">
    {hook h="displayTop"}
  </div>
{/block}

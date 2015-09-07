{* StarterTheme: Improve geolocation restriction *}
{*
{block name="restricted_country"}
  {if isset($restricted_country_mode) && $restricted_country_mode}
    <div id="restricted-country">
      <p>{l s='You cannot place a new order from your country.'}{if isset($geolocation_country) && $geolocation_country} <span class="bold">{$geolocation_country}</span>{/if}</p>
    </div>
  {/if}
{/block}
*}

{block name="header_banner"}
  {hook h='displayBanner'}
{/block}

{block name="header_nav"}
  {hook h='displayNav'}
{/block}

{block name="header_logo"}
  {* StarterTheme: Change the href for homepage link with correct protocol *}
  <a href="#" title="{$shop_name}">
    <img class="logo" src="{$logo_url}" alt="{$shop_name}" />
  </a>
{/block}

{block name="header_top"}
  {hook h='displayTop'}
{/block}

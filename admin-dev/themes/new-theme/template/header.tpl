<header>
  <nav class="main-header">

    {* TODO: BUTTON USED FOR THE MOBILE VERSION TO REACTIVATE *}
    {* TODO: TO REPLACE
    <button id="header_nav_toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-primary">
      <i class="icon-reorder"></i>
    </button>
    *}

    {* Logo *}
    <a class="logo" href="{$default_tab_link|escape:'html':'UTF-8'}"></a>

    <div class="component">{include file="components/layout/quick_access.tpl"}</div>
    {*<div class="component">{include file="components/layout/search_form.tpl"}</div>*}
    <div class="component pull-md-right">{include file="components/layout/employee_dropdown.tpl"}</div>
    <div class="component pull-md-right">{include file="components/layout/notifications_center.tpl"}</div>
    <div class="component pull-md-right">{include file="components/layout/shop_list.tpl"}</div>

    {* TODO: REPLACE THE MAINTENANCE MODE INFORMATION
    {if isset($maintenance_mode) && $maintenance_mode == true}
      <span class="maintenance-mode">
        &mdash;
        <span
          class="label-tooltip"
          data-toggle="tooltip"
          data-placement="bottom"
          data-html="true"r
          title="<p class='text-left text-nowrap'><strong>{l s='Your shop is in maintenance.'}</strong></p><p class='text-left'>{l s='Your visitors and customers cannot access your shop while in maintenance mode.%s To manage the maintenance settings, go to Preferences > Maintenance.' sprintf='<br />'}</p>">{l s='Maintenance mode'}</span>
        </span>
    {/if}
    *}

    {* TODO: REPLACE THE AJAX RUNNING SPINNER WITH THE ONE FROM THE UI KIT
    <span id="ajax_running">
      <i class="icon-refresh icon-spin icon-fw"></i>
    </span>
    *}

    {* TODO: ??? *}
    {if isset($displayBackOfficeTop)}{$displayBackOfficeTop}{/if}

  </nav>
</header>

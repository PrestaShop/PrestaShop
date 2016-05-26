<!DOCTYPE html>
<html lang="{$iso}">
<head>
  {$header}
</head>
<body class="{$smarty.get.controller|escape|strtolower}">

{* TODO: REPLACE THIS CLASSS SOMEWHERE
{if $collapse_menu} page-sidebar-closed{/if}
*}

<header>
  <nav class="main-header">

    {* TODO: BUTTON USED FOR THE MOBILE VERSION TO REACTIVATE *}
    {* TODO: TO REPLACE
    <button id="header_nav_toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-primary">
      <i class="icon-reorder"></i>
    </button>
    *}

    {* Logo *}
    <a class="logo pull-left" href="{$default_tab_link|escape:'html':'UTF-8'}"></a>

    <div class="component pull-left">{include file="components/layout/quick_access.tpl"}</div>
    <div class="component">{include file="components/layout/search_form.tpl"}</div>


    <div class="component pull-md-right -norightmargin">{include file="components/layout/employee_dropdown.tpl"}</div>
    <div class="component pull-md-right">{include file="components/layout/notifications_center.tpl"}</div>
    <div class="component pull-md-right">{include file="components/layout/shop_list.tpl"}</div>
    {if isset($maintenance_mode) && $maintenance_mode == true}
      <div class="component pull-right">
        <div class="shop-state" id="maintenance-mode">
          <i class="material-icons">build</i>
          <a class="label-tooltip" data-toggle="tooltip" data-placement="bottom" data-html="true" title="<p class='text-left text-nowrap'><strong>{l s='Your shop is in maintenance.'}</strong></p><p class='text-left'>{l s='Your visitors and customers cannot access your shop while in maintenance mode.%s To manage the maintenance settings, go to Preferences > Maintenance.' sprintf='<br />'}</p>" href="{$link->getAdminLink('AdminMaintenance')|escape:'html':'UTF-8'}">
              {l s='Maintenance mode'}
          </a>
        </div>
      </div>
    {/if}
    {if isset($debug_mode) && $debug_mode == true}
      <div class="component pull-right">
        <div class="shop-state" id="debug-mode">
          <i class="material-icons">bug_report</i>
          <span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" data-html="true"
            title="<p class='text-left text-nowrap'><strong>{l s='Your shop is in debug mode.'}</strong></p><p class='text-left'>{l s='All PHP errors and messages are displayed, when you are done <strong>turn off</strong> this mode.'}</p>">{l s='Debug mode'}</span>
        </div>
      </div>
    {/if}
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

{include file='components/layout/nav_bar.tpl'}

<div id="main-div">

  {if $install_dir_exists}

    <div class="alert alert-warning">
      {l s='For security reasons, you must also delete the /install folder.'}
    </div>

  {else}

    {if isset($page_header_toolbar)}{$page_header_toolbar}{/if}
    {if isset($modal_module_list)}{$modal_module_list}{/if}

    <div class="content-div {if !isset($page_header_toolbar)}-notoolbar{/if}">

      {* TODO: SEE IF USEFULL
      {if $current_tab_level == 3}with-tabs{/if}
      *}

      {hook h='displayAdminAfterHeader'}

      {* TODO: REPLACE THIS ELEMENT
      {if isset($conf)}
        <div class="bootstrap">
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {$conf}
          </div>
        </div>
      {/if}
      *}

      {include file='components/layout/error_messages.tpl'}
      {include file='components/layout/information_messages.tpl'}
      {include file='components/layout/confirmation_messages.tpl'}
      {include file='components/layout/warning_messages.tpl'}

      <div class="row ">
        <div class="col-xs-12">
          {$page}
        </div>
      </div>

    </div>

  {/if}

</div>

{* TODO: THIS FOOTER WILL BE REMOVED *}
{if $display_footer}
  {include file='footer.tpl'}
{/if}

{if isset($php_errors)}
  {include file="error.tpl"}
{/if}

{if isset($modals)}
  <div class="bootstrap">
    {$modals}
  </div>
{/if}

</body>
</html>

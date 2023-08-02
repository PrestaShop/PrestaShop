<!DOCTYPE html>
<html lang="{$iso}">
<head>
  {render_template
      smarty_template="header.tpl"
      twig_template="@PrestaShop/Admin/Layout/header.html.twig"
      link=$link
      viewport_scale=(isset($viewport_scale)) ? $viewport_scale : null
      img_dir=$img_dir
      meta_title=$meta_title
      shop_name=$shop_name
      display_header_javascript=(isset($display_header_javascript)) ? $display_header_javascript : false
      controller_name=$controller_name
      iso_user=$iso_user
      lang_is_rtl=$lang_is_rtl
      full_language_code=$full_language_code
      full_cldr_language_code=$full_cldr_language_code
      country_iso_code=$country_iso_code
      ps_version=$smarty.const._PS_VERSION_
      round_mode=$round_mode
      shop_context=(isset($shop_context)) ? $shop_context : null
      token=$token
      currentIndex=$currentIndex
      default_language=$default_language
      css_files=(isset($css_files)) ? $css_files : []
      js_def=(isset($js_def) && is_array($js_def)) ? $js_def : []
      js_files=(isset($js_files)) ? $js_files : []
      js_inline=(isset($js_inline)) ? $js_inline : []
      displayBackOfficeHeader=(isset($displayBackOfficeHeader)) ? $displayBackOfficeHeader : null
  }
</head>

<body
  class="lang-{$iso_user}{if $lang_is_rtl} lang-rtl{/if} {$controller_name|escape|strtolower}{if $collapse_menu} page-sidebar-closed{/if}{if isset($is_multishop) && $is_multishop} multishop-enabled{/if}{if isset($lite_display) && $lite_display} light_display_layout{/if}{if !empty($debug_mode)} developer-mode{/if}"
  {if isset($js_router_metadata.base_url)}data-base-url="{$js_router_metadata.base_url}"{/if}
  {if isset($js_router_metadata.token)}data-token="{$js_router_metadata.token}"{/if}
>

{if $display_header}
  <header id="header" class="d-print-none">

    <nav id="header_infos" class="main-header">
      <button class="btn btn-primary-reverse onclick btn-lg unbind ajax-spinner"></button>

      {* Logo *}
      <i class="material-icons js-mobile-menu">menu</i>
      <a id="header_logo" class="logo float-left" href="{$default_tab_link|escape:'html':'UTF-8'}"></a>
      <span id="shop_version">{$ps_version}</span>

      <div class="component" id="quick-access-container">
        {render_template
          smarty_template="components/layout/quick_access.tpl"
          twig_template="@PrestaShop/Admin/Layout/quick_access.html.twig"
          quick_access=$quick_access
          link=$link
          quick_access_current_link_icon=$quick_access_current_link_icon
          quick_access_current_link_name=$quick_access_current_link_name
        }
      </div>
      <div class="component component-search" id="header-search-container">
        <div class="component-search-body">
          <div class="component-search-top">

              {render_template
                smarty_template="components/layout/search_form.tpl"
                twig_template="@PrestaShop/Admin/Layout/search_form.html.twig"
                bo_query=$bo_query
                link=$link
                show_clear_btn= $show_clear_btn|default:null
                search_type= $search_type|default:null
              }
            <button class="component-search-cancel d-none">{l|escape s='Cancel' d='Admin.Actions'}</button>
          </div>
            {render_template
              smarty_template="components/layout/mobile_quickaccess.tpl"
              twig_template="@PrestaShop/Admin/Layout/mobile_quick_access.html.twig"
              quick_access=$quick_access
              link=$link
              quick_access_current_link_icon=$quick_access_current_link_icon
              quick_access_current_link_name=$quick_access_current_link_name
            }
        </div>

        <div class="component-search-background d-none"></div>
      </div>

      {if isset($debug_mode) && $debug_mode == true}
        <div class="component hide-mobile-sm" id="header-debug-mode-container">
          <a class="link shop-state"
             id="debug-mode"
             data-toggle="pstooltip"
             data-placement="bottom"
             data-html="true"
             title="<p class=&quot;text-left&quot;><strong>{l|escape s='Your store is in debug mode.' d='Admin.Navigation.Notification'}</strong></p><p class=&quot;text-left&quot;>{l|escape s='All the PHP errors and messages are displayed. When you no longer need it, [1]turn off[/1] this mode.' html=true sprintf=['[1]' => '<strong>', '[/1]' => '</strong>'] d='Admin.Navigation.Notification'}</p>"
             href="{$link->getAdminLink('AdminPerformance')|escape:'html':'UTF-8'}"
          >
            <i class="material-icons">bug_report</i>
            <span>{l|escape s='Debug mode' d='Admin.Navigation.Header'}</span>
          </a>
        </div>
      {/if}

      {if isset($maintenance_mode) && $maintenance_mode == true}
        {capture name="title"}
          <p class="text-left">
            <strong>{l s='Your store is in maintenance mode.' d='Admin.Navigation.Notification'}</strong>
          </p>
          <p class="text-left">
              {l s='Your visitors and customers cannot access your store while in maintenance mode.' d='Admin.Navigation.Notification'}
          </p>
          <p class="text-left">
              {l s='To manage the maintenance settings, go to Shop Parameters > General > Maintenance tab.' d='Admin.Navigation.Notification'}
          </p>
          {if isset($maintenance_allow_admins) && $maintenance_allow_admins}
            <p class="text-left">
              {l s='Admins can access the store front office without storing their IP.' d='Admin.Navigation.Notification'}
            </p>
          {/if}
        {/capture}
        <div class="component hide-mobile-sm" id="header-maintenance-mode-container">
          <a class="link shop-state"
             id="maintenance-mode"
             data-toggle="pstooltip"
             data-placement="bottom"
             data-html="true"
             title="{$smarty.capture.title|htmlspecialchars}"
             href="{$link->getAdminLink('AdminMaintenance')|escape:'html':'UTF-8'}"
          >
            <i class="material-icons"
              style="{if isset($maintenance_allow_admins) && $maintenance_allow_admins}color: var(--green);{/if}"
            >build</i>
            <span>{l|escape s='Maintenance mode' d='Admin.Navigation.Header'}</span>
          </a>
        </div>
      {/if}

      <div class="header-right">
        {if !isset($hideLegacyStoreContextSelector) || !$hideLegacyStoreContextSelector}
          <div class="component" id="header-shop-list-container">
            {render_template
              smarty_template="components/layout/shop_list.tpl"
              twig_template="@PrestaShop/Admin/Layout/shop_list.html.twig"
              base_url=$base_url
              is_multishop= $is_multishop|default:null
              shop_list= $shop_list|default:null
              multishop_context= $multishop_context|default:null
              current_shop_name= $current_shop_name|default:null
            }
          </div>
        {/if}
        {if $show_new_orders || $show_new_customers || $show_new_messages}
          <div class="component header-right-component" id="header-notifications-container">
              {render_template
                smarty_template="components/layout/notifications_center.tpl"
                twig_template="@PrestaShop/Admin/Layout/notifications_center.html.twig"
              }
          </div>
        {/if}

        <div class="component" id="header-employee-container">
            {render_template
              smarty_template="components/layout/employee_dropdown.tpl"
              twig_template="@PrestaShop/Admin/Layout/employee_dropdown.html.twig"
            }
        </div>
        {if isset($displayBackOfficeTop)}{$displayBackOfficeTop}{/if}
      </div>
    </nav>
  </header>
{/if}

{if $display_header}
    {render_template
      smarty_template="components/layout/nav_bar.tpl"
      twig_template="@PrestaShop/Admin/Layout/nav_bar.html.twig"
      toggle_navigation_url=$toggle_navigation_url
      default_tab_link=$default_tab_link
      ps_version=$ps_version
      collapse_menu=$collapse_menu
      tabs=$tabs
    }
{/if}

{if isset($page_header_toolbar_template)}
    {render_template
      smarty_template=$page_header_toolbar_template
      twig_template="@PrestaShop/Admin/Layout/tool_bar.html.twig"
      multistore_header=$multistore_header
      breadcrumbs2=$breadcrumbs2
      title=$title
      toolbar_btn=$toolbar_btn
      table=$table
      help_link=$help_link
      enableSidebar=$enableSidebar
      current_tab_level=$current_tab_level
    }
{/if}

<div id="main-div">
    {if $install_dir_exists}
      <div class="alert alert-warning">
        {l|escape s='For security reasons, you must also delete the /install folder.' d='Admin.Login.Notification'}
      </div>
    {else}
      {if isset($modal_module_list)}{$modal_module_list}{/if}

      <div class="{if $display_header}content-div{/if} {if !isset($page_header_toolbar)}-notoolbar{/if} {if $current_tab_level == 3}with-tabs{/if}">

        {hook h='displayAdminAfterHeader'}

        {if $display_header}
          {include file='components/layout/error_messages.tpl'}
          {include file='components/layout/information_messages.tpl'}
          {include file='components/layout/confirmation_messages.tpl'}
          {include file='components/layout/warning_messages.tpl'}
        {/if}

        {$page}
        {hook h='displayAdminEndContent'}

      </div>
    {/if}
</div>

{if (!isset($lite_display) || (isset($lite_display) && !$lite_display))}
    {render_template
      smarty_template="components/layout/non-responsive.tpl"
      twig_template="@PrestaShop/Admin/Layout/non_responsive.html.twig"
      default_tab_link=$default_tab_link
    }
  <div class="mobile-layer"></div>

  {if $display_footer}
      {render_template
        smarty_template="footer.tpl"
        twig_template="@PrestaShop/Admin/Layout/footer.html.twig"
      }
  {/if}
{/if}

{if isset($php_errors)}
  {include file="error.tpl"}
{/if}

{if (!isset($lite_display) || (isset($lite_display) && !$lite_display))}
  {if isset($modals)}
    <div class="bootstrap">
      {$modals}
    </div>
  {/if}
{/if}

</body>
</html>

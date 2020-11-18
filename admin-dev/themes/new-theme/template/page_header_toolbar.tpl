{* TODO: 1.7.0.0: RENAME THIS FILE AT THE NEXT RETROCOMPATIBILITY BREAK *}

<div class="header-toolbar">
  <div class="container-fluid">

    {block name=pageBreadcrumb}
      <nav aria-label="Breadcrumb">
        <ol class="breadcrumb">
          {if $breadcrumbs2.container.name != ''}
            <li class="breadcrumb-item">{$breadcrumbs2.container.name|escape}</li>
          {/if}

          {if $breadcrumbs2.tab.name != '' && $breadcrumbs2.container.name != $breadcrumbs2.tab.name && $breadcrumbs2.tab.href != ''}
            <li class="breadcrumb-item active">
              <a href="{$breadcrumbs2.tab.href|escape}" aria-current="page">{$breadcrumbs2.tab.name|escape}</a>
            </li>
          {/if}
        </ol>
      </nav>
    {/block}

    <div class="title-row">
      {block name=pageTitle}
          <h1 class="title">
            {if is_array($title)}{$title|end|escape}{else}{$title|escape}{/if}
          </h1>
      {/block}

      {block name=toolbarBox}
        <div class="toolbar-icons">
          <div class="wrapper">
            {hook h='displayDashboardToolbarTopMenu'}
            {foreach from=$toolbar_btn item=btn key=k}
              {if $k != 'back' && $k != 'modules-list'}
                {* TODO: REFACTOR ALL THIS THINGS *}
                <a
                  class="btn btn-primary {if isset($btn.target) && $btn.target} _blank{/if} pointer"{if isset($btn.href)}
                  id="page-header-desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass|escape}{else}{$k}{/if}"
                  href="{$btn.href|escape}"{/if}
                  title="{if isset($btn.help)}{$btn.help}{else}{$btn.desc|escape}{/if}"{if isset($btn.js) && $btn.js}
                  onclick="{$btn.js}"{/if}{if isset($btn.modal_target) && $btn.modal_target}
                  data-target="{$btn.modal_target}"
                  data-toggle="modal"{/if}{if isset($btn.help)}
                  data-toggle="pstooltip"
                  data-placement="bottom"{/if}
                >
                  {if !empty($btn.icon)}<i class="material-icons">{$btn.icon}</i>{/if}
                  {$btn.desc|escape}
                </a>
              {/if}
            {/foreach}
            {if isset($toolbar_btn['modules-list'])}
              {* TODO: REFACTOR ALL THIS THINGS *}
              <a
                class="btn btn-outline-secondary {if isset($toolbar_btn['modules-list'].target) && $toolbar_btn['modules-list'].target} _blank{/if}"
                id="page-header-desc-{$table}-{if isset($toolbar_btn['modules-list'].imgclass)}{$toolbar_btn['modules-list'].imgclass}{else}modules-list{/if}"
                {if isset($toolbar_btn['modules-list'].href)}href="{$toolbar_btn['modules-list'].href}"{/if}
                title="{$toolbar_btn['modules-list'].desc}"
                {if isset($toolbar_btn['modules-list'].js) && $toolbar_btn['modules-list'].js}onclick="{$toolbar_btn['modules-list'].js}"{/if}
              >
                {$toolbar_btn['modules-list'].desc}
              </a>
            {/if}

            {if isset($help_link) and $help_link != false}

              {if $enableSidebar}
                <a class="btn btn-outline-secondary btn-help btn-sidebar" href="#"
                   title="{l s='Help'}"
                   data-toggle="sidebar"
                   data-target="#right-sidebar"
                   data-url="{$help_link|escape}"
                   id="product_form_open_help"
                >
                  {l s='Help'}
                </a>
              {else}
                <a class="btn btn-outline-secondary" href="{$help_link|escape}" title="{l s='Help'}">
                  {l s='Help'}
                </a>
              {/if}
            {/if}
          </div>
        </div>
      {/block}
    </div>
  </div>

  {if isset($headerTabContent) and $headerTabContent}
    <div class="page-head-tabs" id="head_tabs">
    {foreach $headerTabContent as $tabContent}
        {{$tabContent}}
    {/foreach}
    </div>
  {/if}

  {if $current_tab_level == 3}
    <div class="page-head-tabs" id="head_tabs">
      <ul class="nav nav-pills">
      {foreach $tabs as $level_1}
        {foreach $level_1.sub_tabs as $level_2}
          {foreach $level_2.sub_tabs as $level_3}
            {if $level_3.current}
              {foreach $level_3.sub_tabs as $level_4}
                {if $level_4.active}
                  <li class="nav-item">
                    <a href="{$level_4.href}" id="subtab-{$level_4.class_name}" class="nav-link tab {if $level_4.current}active current{/if}" data-submenu="{$level_4.id_tab}">
                      {$level_4.name}
                      <span class="notification-container">
                        <span class="notification-counter"></span>
                      </span>
                    </a>
                  </li>
                {/if}
              {/foreach}
            {/if}
          {/foreach}
        {/foreach}
      {/foreach}
      </ul>
    </div>
  {/if}
  {hook h='displayDashboardTop'}
</div>

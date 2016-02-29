{* TODO: 1.7.0.0: RENAME THIS FILE AT THE NEXT RETROCOMPATIBILITY BREAK *}

<div class="header-toolbar">

  {block name=pageBreadcrumb}
    <ol class="breadcrumb">

      {if $breadcrumbs2.container.name != ''}
        <li>
          {if $breadcrumbs2.container.href != ''}
            <a href="{$breadcrumbs2.container.href|escape}">{$breadcrumbs2.container.name|escape}</a>
          {/if}
        </li>
      {/if}

      {if $breadcrumbs2.tab.name != '' && $breadcrumbs2.container.name != $breadcrumbs2.tab.name}
        <li>
          {if $breadcrumbs2.tab.href != ''}
            <a href="{$breadcrumbs2.tab.href|escape}">{$breadcrumbs2.tab.name|escape}</a>
          {/if}
        </li>
      {/if}

    </ol>
  {/block}

  {block name=pageTitle}
    <h2 class="title">
      {if is_array($title)}{$title|end|escape}{else}{$title|escape}{/if}
    </h2>
  {/block}

  {block name=toolbarBox}
    <div class="toolbar-icons">
      {foreach from=$toolbar_btn item=btn key=k}
        {if $k != 'back' && $k != 'modules-list'}
          {* TODO: REFACTOR ALL THIS THINGS *}
          <a id="page-header-desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass|escape}{else}{$k}{/if}" class="toolbar-button toolbar_btn {if isset($btn.target) && $btn.target} _blank{/if} pointer"{if isset($btn.href)} href="{$btn.href|escape}"{/if} title="{if isset($btn.help)}{$btn.help}{else}{$btn.desc|escape}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}{if isset($btn.modal_target) && $btn.modal_target} data-target="{$btn.modal_target}" data-toggle="modal"{/if}{if isset($btn.help)} data-toggle="tooltip" data-placement="bottom"{/if}>
            <i class="material-icons">{$btn.icon}</i>
            <span class="title">{$btn.desc|escape}</span>
          </a>
        {/if}
      {/foreach}
      {if isset($toolbar_btn['modules-list'])}
        {* TODO: REFACTOR ALL THIS THINGS *}
        <a id="page-header-desc-{$table}-{if isset($toolbar_btn['modules-list'].imgclass)}{$toolbar_btn['modules-list'].imgclass}{else}modules-list{/if}" class="toolbar-button toolbar_btn{if isset($toolbar_btn['modules-list'].class)} {$toolbar_btn['modules-list'].class}{/if}{if isset($toolbar_btn['modules-list'].target) && $toolbar_btn['modules-list'].target} _blank{/if}" {if isset($toolbar_btn['modules-list'].href)}href="{$toolbar_btn['modules-list'].href}"{/if} title="{$toolbar_btn['modules-list'].desc}"{if isset($toolbar_btn['modules-list'].js) && $toolbar_btn['modules-list'].js} onclick="{$toolbar_btn['modules-list'].js}"{/if}>
          {if isset($toolbar_btn['modules-list'].imgclass)}
            <i class="process-icon-{$toolbar_btn['modules-list'].imgclass}"></i>
          {else}
            <i class="material-icons">extension</i>
          {/if}
          <span class="title">{$toolbar_btn['modules-list'].desc}</span>
        </a>
      {/if}
      {if isset($help_link)}
        <a class="toolbar-button" href="{$help_link|escape}" title="{l s='Help'}">
          <i class="material-icons">help</i>
          <span class="title">{l s='Help'}</span>
        </a>
      {/if}
    </div>
  {/block}
  {if isset($headerTabContent)}
      <div class="page-head-tabs">
      {foreach $headerTabContent as $tabContent}
          {{$tabContent}}
      {/foreach}
      </div>
  {/if}
  {if $current_tab_level == 3}
    <div class="page-head-tabs">
      {foreach $tabs as $level_1}
        {foreach $level_1.sub_tabs as $level_2}
          {foreach $level_2.sub_tabs as $level_3}
            {if $level_3.current}
              {foreach $level_3.sub_tabs as $level_4}
                <a href="{$level_4.href}" {if $level_4.current}class="current"{/if}>{$level_4.name}</a>
              {/foreach}
            {/if}
          {/foreach}
        {/foreach}
      {/foreach}
    </div>
  {/if}

</div>

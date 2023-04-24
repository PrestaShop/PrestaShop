{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<script type="text/javascript">
   $(function() {
      var id_tab_parentmodule = {$id_tab_parentmodule|intval};
      var id_tab_module = {$id_tab_module|intval};
      $('tr.child-'+id_tab_parentmodule+' > td > input.view.'+id_tab_module).on('change', function () {
         if (!$(this).prop('checked'))
         {
            $('#table_module_2 thead th:eq(1) input').trigger('click');
            if ($('#table_module_2 thead th:eq(1) input').prop('checked'))
               $('#table_module_2 thead th:eq(1) input').trigger('click');
         }
      });
      $('tr.child-'+id_tab_parentmodule+' > td > input.edit.'+id_tab_module).on('change', function () {
         if (!$(this).prop('checked'))
         {
            $('#table_module_2 thead th:eq(2) input').trigger('click');
            if ($('#table_module_2 thead th:eq(2) input').prop('checked'))
               $('#table_module_2 thead th:eq(2) input').trigger('click');
         }
      });

      $('div.productTabs').find('a').each(function() {
         $(this).attr('href', '#');
      });
      $('div.productTabs a').on('click', function() {
         var id = $(this).attr('id');
         $('.nav-profile').removeClass('selected');
         $(this).addClass('selected active');
         $(this).siblings().removeClass('active');
         $('.tab-profile').hide()
         $('.'+id).show();
      });
      function getChildren(table, perm, parent, rel) {
         var kids = document.querySelectorAll(table+" [data-parent='"+parent+"'][data-type='"+perm+"']:not([data-rel='"+rel+"'])");
         for(var i=0; i<kids.length;i++)
         {
            if(kids[i].checked) {
               return true;
            }
         }
         return false;
      }
      $('.ajaxPower').on('change', function(){
         var tout = $(this).data('rel').split('||');
         var rel = $(this).data('rel');
         var id_tab = tout[0];
         var id_profile = tout[1];
         var perm = tout[2];
         var enabled = $(this).is(':checked')? 1 : 0;
         var tabsize = tout[3];
         var tabnumber = tout[4];
         var table = 'table#table_'+id_profile;
         var $parentRow = ($(this).parents('tr'));
         var classes = $parentRow.attr('class');
         var $permissionCheckbox = $(this);
         var targetPermissionType;
         var parent = $(this).attr('data-parent');
         if (parent != 0){
            var $parentelem = $(table + ' .ajaxPower.' + perm + '.' + parent);
            if(!$parentelem.is(':checked')){
               $parentelem.prop("checked", true).change();
            }else{
               if(!$(this).is(':checked') && !getChildren(table, perm, parent, rel))
                  $parentelem.prop("checked", false).change();
            }
         }
         switch (true) {
            case $permissionCheckbox.hasClass('view'): targetPermissionType = '.view'; break;
            case $permissionCheckbox.hasClass('add'): targetPermissionType = '.add'; break;
            case $permissionCheckbox.hasClass('delete'): targetPermissionType = '.delete'; break;
            case $permissionCheckbox.hasClass('edit'): targetPermissionType = '.edit'; break;
         }
         if (enabled && classes) {
            var subject = 'child';
            var subjectIndex = classes.indexOf(subject);
            if (-1 !== subjectIndex) {
               var subjectIndexes = [];
               while (-1 !== subjectIndex) {
                  subjectIndexes.push(subjectIndex);
                  subjectIndex = classes.indexOf(subject, subjectIndex + 1);
               }
               subjectIndexes.pop();
               var classAttribute = subject + '-';
               var targetClass;
               var $parentPermissions;
               var $parentPermission;
               while (subjectIndexes.length > 0) {
                  targetClass = new Array(subjectIndexes.length + 1).join(classAttribute);
                  $parentPermissions = $parentRow.prevAll().filter(function (rowIndex, row) {
                     return $(row).attr('class').match(new RegExp('^' + targetClass + '[0-9]'));
                  });
                  $parentPermission = $($parentPermissions[0]).find(targetPermissionType);
                  if (!$parentPermission.prop('checked')) {
                     $parentPermission.prop('checked', true);
                  }
                  subjectIndexes.pop();
               }
               var $topParent = $($parentRow.prevAll().filter('.parent')[0]).find(targetPermissionType);
               if (!$topParent.prop('checked')) {
                  $topParent.prop('checked', true);
               }
            }
         }
         if (perm == 'all' && $(this).parent().parent().hasClass('parent'))
         {
            if (enabled)
               $(this).parent().parent().parent().find('.child-'+id_tab+' input[type=checkbox]').attr('checked', 'checked');
            else
               $(this).parent().parent().parent().find('.child-'+id_tab+' input[type=checkbox]').removeAttr('checked');
            $.ajax({
               url: "{$link->getAdminLink('AdminAccess')|addslashes}",
               cache: false,
               data : {
                  ajaxMode : '1',
                  id_tab: id_tab,
                  id_profile: id_profile,
                  perm: perm,
                  enabled: enabled,
                  submitAddAccess: '1',
                  addFromParent: '1',
                  action: 'updateAccess',
                  ajax: '1',
                  token: '{getAdminToken tab='AdminAccess'}'
               },
               success : function(res,textStatus,jqXHR)
               {
                  try {
                     if (res == 'ok')
                        showSuccessMessage("{l s='Update successful' d='Admin.Notifications.Success'}");
                     else
                        showErrorMessage("{l s='Unable to update settings.' d='Admin.Notifications.Error'}");
                  } catch(e) {
                     jAlert('Technical error');
                  }
               }
            });
         }
         perfect_access_js_gestion(this, perm, id_tab, tabsize, tabnumber, table);
         $.ajax({
            url: "{$link->getAdminLink('AdminAccess')|addslashes}",
            cache: false,
            data : {
               ajaxMode : '1',
               id_tab: id_tab,
               id_profile: id_profile,
               perm: perm,
               enabled: enabled,
               submitAddAccess: '1',
               action: 'updateAccess',
               ajax: '1',
               token: '{getAdminToken tab='AdminAccess'}'
            },
            success : function(res,textStatus,jqXHR)
            {
               try
               {
                  if (res == 'ok')
                     showSuccessMessage("{l s='Update successful' d='Admin.Notifications.Success'}");
                  else
                     showErrorMessage("{l s='Unable to update settings.' d='Admin.Notifications.Error'}");
               }
               catch(e)
               {
                  jAlert('Technical error');
               }
            }
         });
      });
      $(".changeModuleAccess").on('change', function(){
         var tout = $(this).data('rel').split('||');
         var id_module = tout[0];
         var perm = tout[1];
         var id_profile = tout[2];
         var enabled = $(this).is(':checked') ? 1 : 0;
         var enabled_attr = $(this).is(':checked') ? true : false;
         var table = 'table#table_module_'+id_profile;
         if (id_module == -1)
            $(table+' .ajax-ma-'+perm).each(function(key, value) {
               $(this).attr("checked", enabled_attr);
            });
         else if (!enabled)
            $(table+' #ajax-ma-'+perm+'-master').each(function(key, value) {
               $(this).attr("checked", enabled_attr);
            });
         $.ajax({
            url: "{$link->getAdminLink('AdminAccess')|addslashes}",
            cache: false,
            data : {
               ajaxMode: '1',
               id_module: id_module,
               perm: perm,
               enabled: enabled,
               id_profile: id_profile,
               changeModuleAccess: '1',
               action: 'updateModuleAccess',
               ajax: '1',
               token: '{getAdminToken tab='AdminAccess'}'
            },
            success : function(res,textStatus,jqXHR)
            {
               try
               {
                  if (res == 'ok')
                     showSuccessMessage("{l s='Update successful' d='Admin.Notifications.Success'}");
                  else
                     showErrorMessage("{l s='Unable to update settings.' d='Admin.Notifications.Error'}");
               }
               catch(e)
               {
                  jAlert('Technical error');
               }
            }
         });
      });
   });
</script>
<div class="row">
  <div class="productTabs col-lg-2">
    <div class="tab list-group">
      {foreach $profiles as $profile}
        <a class="list-group-item nav-profile {if $profile.id_profile == $current_profile}active{/if}"
           id="profile-{$profile.id_profile}"
           href="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;id_profile={$profile.id_profile}">{$profile.name}</a>
      {/foreach}
    </div>
  </div>
  <form id="{$table}_form" class="defaultForm form-horizontal col-lg-10"
        action="{$current|escape:'html':'UTF-8'}&amp;{$submit_action}=1&amp;token={$token|escape:'html':'UTF-8'}"
        method="post" enctype="multipart/form-data">
    {if $form_id}
      <input type="hidden" name="{$identifier}" id="{$identifier}" value="{$form_id}"/>
    {/if}
    {assign var=tabsize value=count($tabs)}
    {foreach $tabs AS $tab}
      {if $tab.id_tab > $tabsize}
        {assign var=tabsize value=$tab.id_tab}
      {/if}
    {/foreach}
    {foreach $profiles as $profile}
      <div class="profile-{$profile.id_profile} tab-profile"
           style="display:{if $profile.id_profile != $current_profile}none{/if}">
        <div class="row">
          {if $profile.id_profile != $admin_profile}
            <div class="col-lg-6">
              <div class="panel">
                <h3>{l s='Menu' d='Admin.Advparameters.Feature'}</h3>
                <table class="table" id="table_{$profile.id_profile}">
                  {assign var=enable_bulk_view value=true}
                  {assign var=enable_bulk_add value=true}
                  {assign var=enable_bulk_edit value=true}
                  {assign var=enable_bulk_delete value=true}
                  {assign var=enable_bulk_all value=true}
                  <tbody>
                  {if !count($tabs)}
                    <tr>
                      <td colspan="6">{l s='No menu' d='Admin.Advparameters.Feature'}</td>
                    </tr>
                  {else}
                    {foreach $tabs AS $tab}
                      {assign var=access value=$accesses[$profile.id_profile]}
                      {assign var=result_accesses value=0}
                      <tr class="parent">
                        <td class="bold"> &raquo;<strong>{$tab.name}</strong></td>
                        {assign var=has_all_rights value=true}
                        {foreach $perms as $perm}
                          {assign var=id_perm value=$id_perms[$perm]}
                          {if $access_edit == 1 && $accesses[$employee_profile_id][$tab.id_tab][$perm]}
                            <td>
                              <input type="checkbox"
                                     data-id="{$id_perm}"
                                     data-parent="0"
                                     data-type="{$perm}"
                                     data-classname="{$tab.class_name}"
                                     data-rel="{$access[$tab.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}"
                                     class="ajaxPower {$perm} {$access[$tab.id_tab]['id_tab']}"
                                     {if $access[$tab.id_tab][$perm] == 1} checked="checked"{/if}
                              />
                            </td>
                          {else}
                            {assign var=has_all_rights value=false}
                            {assign var=enable_bulk_view value=false}
                            <td>
                              <input type="checkbox"
                                     disabled="disabled"
                                     {if $access[$tab.id_tab][$perm] == 1} checked="checked"{/if}
                              />
                            </td>
                          {/if}
                          {assign var=result_accesses value=$result_accesses + $access[$tab.id_tab][$perm]}
                        {/foreach}
                        <td>
                          <input
                            type="checkbox"
                            {if $access_edit == 1 && $has_all_rights}
                              data-id="4"
                              data-parent="0"
                              data-type="all"
                              data-classname="{$tab.class_name}"
                              data-rel="{$access[$tab.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"
                              class="ajaxPower all {$access[$tab.id_tab]['id_tab']}"
                            {else}
                              class="all {$access[$tab.id_tab]['id_tab']}"
                              disabled="disabled"
                            {/if}
                            {if $result_accesses == 4} checked="checked"{/if}
                          />
                        </td>
                      </tr>
                      {if count($tab.children)}
                        {foreach $tab.children AS $child}
                          {if $child.id_parent === $tab.id_tab}
                            {if isset($access[$child.id_tab])}
                              {assign var=is_child value=true}
                              {assign var=result_accesses value=0}
                              <tr class="child-{$child.id_parent}">
                                <td class="bold"> &raquo; {$child.name}</td>
                                {assign var=has_all_rights value=true}
                                {foreach $perms as $perm}
                                  {assign var=id_perm value=$id_perms[$perm]}
                                  {if $access_edit == 1 && $accesses[$employee_profile_id][$child.id_tab][$perm]}
                                    <td>
                                      <input type="checkbox"
                                             data-id="{$id_perm}" data-parent="{$child.id_parent}"
                                             data-type="{$perm}"
                                             data-classname="{$child.class_name}"
                                             data-rel="{$access[$child.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}"
                                             class="ajaxPower {$perm} {$access[$child.id_tab]['id_tab']}"
                                             {if $access[$child.id_tab][$perm] == 1} checked="checked"{/if}
                                      />
                                    </td>
                                  {else}
                                    {assign var=has_all_rights value=false}
                                    {assign var=enable_bulk_add value=false}
                                    <td>
                                      <input type="checkbox"
                                             disabled="disabled"{if $access[$child.id_tab][$perm] == 1} checked="checked"{/if}/>
                                    </td>
                                  {/if}
                                  {assign var=result_accesses value=$result_accesses + $access[$child.id_tab][$perm]}
                                {/foreach}
                                <td>
                                  <input
                                    type="checkbox"
                                    {if $access_edit == 1 && $has_all_rights}
                                      data-id="4"
                                      data-parent="{$child.id_parent}"
                                      data-type="all"
                                      data-classname="{$child.class_name}"
                                      data-rel="{$access[$child.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"
                                      class="ajaxPower all {$access[$child.id_tab]['id_tab']}"
                                    {else}
                                      class="all {$access[$child.id_tab]['id_tab']}"
                                      disabled="disabled"
                                    {/if}
                                    {if $result_accesses == 4} checked="checked"{/if}
                                  />
                                </td>
                              </tr>
                            {/if}

                            {if count($child.children)}
                              {foreach $child.children AS $subChild}
                                {if isset($access[$subChild.id_tab])}
                                  {assign var=is_child value=true}
                                  {assign var=result_accesses value=0}
                                  <tr class="child-child-{$subChild.id_parent}">
                                    <td class="bold"> &nbsp; &nbsp; &raquo; {$subChild.name}</td>
                                    {assign var=has_all_rights value=true}
                                    {foreach $perms as $perm}
                                      {assign var=id_perm value=$id_perms[$perm]}
                                      {if $access_edit == 1 && $accesses[$employee_profile_id][$subChild.id_tab][$perm]}
                                        <td>
                                          <input type="checkbox"
                                                 data-id="{$id_perm}"
                                                 data-parent="{$subChild.id_parent}"
                                                 data-type="{$perm}"
                                                 data-classname="{$subChild.class_name}"
                                                 data-rel="{$access[$subChild.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}"
                                                 class="ajaxPower {$perm} {$access[$subChild.id_tab]['id_tab']}"
                                                 {if $access[$subChild.id_tab][$perm] == 1} checked="checked"{/if}
                                          />
                                        </td>
                                      {else}
                                        {assign var=has_all_rights value=false}
                                        {assign var=enable_bulk_edit value=false}
                                        <td>
                                          <input type="checkbox"
                                                 disabled="disabled"
                                                 {if $access[$subChild.id_tab][$perm] == 1} checked="checked"{/if}
                                          />
                                        </td>
                                      {/if}
                                      {assign var=result_accesses value=$result_accesses + $access[$subChild.id_tab][$perm]}
                                    {/foreach}
                                    <td>
                                      <input
                                        type="checkbox"
                                        {if $access_edit == 1 && $has_all_rights}
                                          data-id="4"
                                          data-parent="{$subChild.id_parent}"
                                          data-type="all"
                                          data-classname="{$subChild.class_name}"
                                          data-rel="{$access[$subChild.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"
                                          class="ajaxPower all {$access[$subChild.id_tab]['id_tab']}"
                                        {else}
                                          class="all {$access[$subChild.id_tab]['id_tab']}"
                                          disabled="disabled"
                                        {/if}
                                        {if $result_accesses == 4}checked="checked"{/if}
                                      />
                                    </td>
                                  </tr>
                                  {if count($subChild.children)}
                                    {foreach $subChild.children AS $subsubChild}
                                      {if isset($access[$subsubChild.id_tab])}
                                        {assign var=is_child value=true}
                                        {assign var=result_accesses value=0}
                                        <tr class="child-child-child-{$subsubChild.id_parent}">
                                          <td class="bold">&nbsp; &nbsp; &nbsp; &nbsp; &raquo; {$subsubChild.name}</td>
                                          {assign var=has_all_rights value=true}
                                          {foreach $perms as $perm}
                                            {assign var=id_perm value=$id_perms[$perm]}
                                            {if $access_edit == 1  && $accesses[$employee_profile_id][$subsubChild.id_tab][$perm]}
                                              <td>
                                                <input type="checkbox" data-id="{$id_perm}"
                                                       data-parent="{$subsubChild.id_parent}"
                                                       data-type="{$perm}"
                                                       data-classname="{$subsubChild.class_name}"
                                                       data-rel="{$access[$subsubChild.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}"
                                                       class="ajaxPower {$perm} {$access[$subsubChild.id_tab]['id_tab']}"
                                                       {if $access[$subsubChild.id_tab][$perm] == 1} checked="checked"{/if}
                                                />
                                              </td>
                                            {else}
                                              {assign var=has_all_rights value=false}
                                              {assign var=enable_bulk_delete value=false}
                                              <td>
                                                <input type="checkbox"
                                                       disabled="disabled"
                                                       {if $access[$subsubChild.id_tab][$perm] == 1} checked="checked"{/if}
                                                />
                                              </td>
                                            {/if}
                                            {assign var=result_accesses value=$result_accesses + $access[$subsubChild.id_tab][$perm]}
                                          {/foreach}
                                          <td>
                                            <input
                                              type="checkbox"
                                              {if $access_edit == 1  && $has_all_rights}
                                                data-id="4"
                                                data-parent="{$subChild.id_parent}"
                                                data-type="all"
                                                data-classname="{$subsubChild.class_name}"
                                                data-rel="{$access[$subsubChild.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"
                                                class="ajaxPower all {$access[$subsubChild.id_tab]['id_tab']}"
                                              {else}
                                                {assign var=enable_bulk_all value=false}
                                                class="all {$access[$subsubChild.id_tab]['id_tab']}"
                                                disabled="disabled"
                                              {/if}
                                              {if $result_accesses == 4} checked="checked"{/if}
                                            />
                                          </td>
                                        </tr>
                                      {/if}
                                    {/foreach}
                                  {/if}
                                {/if}
                              {/foreach}
                            {/if}
                          {/if}
                        {/foreach}
                      {/if}
                    {/foreach}
                  {/if}
                  </tbody>
                  <thead>
                  <tr>
                    <th></th>
                    <th>
                      <input type="checkbox" name="1"
                             class="viewall ajaxPower"{if $access_edit == 1 && $enable_bulk_view} data-id="0" data-parent="0" data-type="view" data-rel="-1||{$profile.id_profile}||view||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
                      {l s='View' d='Admin.Actions'}
                    </th>
                    <th>
                      <input type="checkbox" name="1"
                             class="addall ajaxPower"{if $access_edit == 1 && $enable_bulk_add} data-id="1" data-parent="0" data-type="add" data-rel="-1||{$profile.id_profile}||add||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
                      {l s='Add' d='Admin.Actions'}
                    </th>
                    <th>
                      <input type="checkbox" name="1"
                             class="editall ajaxPower"{if $access_edit == 1 && $enable_bulk_edit} data-id="2" data-parent="0" data-type="edit" data-rel="-1||{$profile.id_profile}||edit||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
                      {l s='Edit' d='Admin.Actions'}
                    </th>
                    <th>
                      <input type="checkbox" name="1"
                             class="deleteall ajaxPower"{if $access_edit == 1 && $enable_bulk_delete} data-id="3" data-parent="0" data-type="delete" data-rel="-1||{$profile.id_profile}||delete||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
                      {l s='Delete' d='Admin.Actions'}
                    </th>
                    <th>
                      <input type="checkbox" name="1"
                             class="allall ajaxPower"{if $access_edit == 1 && $enable_bulk_all} data-id="4" data-parent="0" data-type="all" data-rel="-1||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
                      {l s='All' d='Admin.Global'}
                    </th>
                  </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="panel">
                <h3>{l s='Modules' d='Admin.Global'}</h3>
                <table class="table" id="table_module_{$profile.id_profile}">
                  <tbody>
                  {if !count($modules)}
                    <tr>
                      <td colspan="3">{l s='No module has been installed.' d='Admin.Notifications.Warning'}</td>
                    </tr>
                  {else}
                    {assign var=enable_bulk_view value=true}
                    {assign var=enable_bulk_configure value=true}
                    {assign var=enable_bulk_uninstall value=true}
                    {foreach from=$modules[$profile.id_profile] item=module key=k}
                      <tr>
                        <td>&raquo; {$module.name}</td>
                        <td>
                          <input type="checkbox"
                                 value="1"{if $module.view == true} checked="checked"{/if}{if $access_edit == 1 && $modules[$employee_profile_id][$k].view} class="ajax-ma-view changeModuleAccess" data-rel="{$module.id_module}||view||{$profile.id_profile}"{else}{assign var=enable_bulk_view value=false} class="ajax-ma-view" disabled="disabled"{/if}/>
                        </td>
                        <td>
                          <input type="checkbox"
                                 value="1"{if $module.configure == true} checked="checked"{/if}{if $access_edit == 1 && $modules[$employee_profile_id][$k].configure} class="ajax-ma-configure changeModuleAccess" data-rel="{$module.id_module}||configure||{$profile.id_profile}"{else}{assign var=enable_bulk_configure value=false} class="ajax-ma-configure" disabled="disabled"{/if}/>
                        </td>
                        <td>
                          <input type="checkbox"
                                 value="1"{if $module.uninstall == true} checked="checked"{/if}{if $access_edit == 1 && $modules[$employee_profile_id][$k].uninstall} class="ajax-ma-uninstall changeModuleAccess" data-rel="{$module.id_module}||uninstall||{$profile.id_profile}"{else}{assign var=enable_bulk_uninstall value=false} class="ajax-ma-uninstall" disabled="disabled"{/if}/>
                        </td>
                      </tr>
                    {/foreach}
                  {/if}
                  </tbody>
                  <thead>
                  <tr>
                    <th></th>
                    <th>
                      <input
                        type="checkbox"{if $access_edit == 1 && $enable_bulk_view} class="changeModuleAccess" data-rel="-1||view||{$profile.id_profile}"{else} disabled="disabled"{/if}/> {l s='View' d='Admin.Actions'}
                    </th>
                    <th>
                      <input
                        type="checkbox"{if $access_edit == 1 && $enable_bulk_configure} class="changeModuleAccess" data-rel="-1||configure||{$profile.id_profile}"{else} disabled="disabled"{/if}/> {l s='Configure' d='Admin.Actions'}
                    </th>
                    <th>
                      <input
                        type="checkbox"{if $access_edit == 1 && $enable_bulk_uninstall} class="changeModuleAccess" data-rel="-1||uninstall||{$profile.id_profile}"{else} disabled="disabled"{/if}/> {l s='Uninstall' d='Admin.Actions'}
                    </th>
                  </tr>
                  </thead>
                </table>
              </div>
            </div>
          {else}
            <div class="col-lg-12">
              <div class="panel">
                {l s='Administrator permissions cannot be modified.' d='Admin.Advparameters.Notification'}
              </div>
            </div>
          {/if}
        </div>
      </div>
    {/foreach}
  </form>
</div>

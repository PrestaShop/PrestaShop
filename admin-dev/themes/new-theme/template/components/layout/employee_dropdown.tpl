<div class="employee-dropdown dropdown">
  {if isset($employee)}
    <img class="avatar dropdown-toggle img-circle" data-toggle="dropdown" src="{$employee->getImage()}" />
  {/if}
  <div class="dropdown-menu dropdown-menu-right p-a-1">
    <div class="text-xs-center">
      <img class="avatar img-circle" src="{$employee->getImage()}" /><br />
      {$employee->firstname} {$employee->lastname}
    </div>
    <div class="dropdown-divider"></div>
    <a href="{$link->getAdminLink('AdminEmployees')|escape:'html':'UTF-8'}&amp;id_employee={$employee->id|intval}&amp;updateemployee" target="_blank" class="small employee-link">
      <i class="material-icons">build</i> {l s='My Preferences'}
    </a>
    <div class="dropdown-divider"></div>
    <a id="header_logout" href="{$login_link|escape:'html':'UTF-8'}&amp;logout">
      <i class="material-icons">exit_to_app</i> {l s='Sign out'}
    </a>
  </div>
</div>

<div class="employee-dropdown dropdown">
  {if isset($employee)}
    <div class="img-circle person" data-toggle="dropdown">
      <i class="material-icons">person</i>
    </div>
  {/if}
  <div class="dropdown-menu dropdown-menu-right p-a-1 m-r-2">
    <div class="text-xs-center">
      <img class="avatar img-circle" src="{$employee->getImage()}" /><br>
      {$employee->firstname} {$employee->lastname}
    </div>
    <hr>
    <a class="employee-link" href="{$link->getAdminLink('AdminEmployees')|escape:'html':'UTF-8'}&amp;id_employee={$employee->id|intval}&amp;updateemployee" target="_blank">
      <i class="material-icons">settings_applications</i> {l s='Your profile'}
    </a>
    <a class="employee-link m-t-1" id="header_logout" href="{$login_link|escape:'html':'UTF-8'}&amp;logout">
      <i class="material-icons">power_settings_new</i> {l s='Log out'}
    </a>
  </div>
</div>

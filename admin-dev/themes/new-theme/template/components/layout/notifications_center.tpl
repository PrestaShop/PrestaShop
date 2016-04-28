<div class="notification-center dropdown">
  <div class="notification dropdown-toggle" data-toggle="dropdown">
    <i class="material-icons">notifications</i>
    {* TODO: REFACTORING THIS IDENTIFIER *}
    <span id="orders_notif_value" class="count">0</span>
  </div>
  <div class="dropdown-menu dropdown-menu-right">
    <div class="notifications">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#orders-notifications" role="tab">{l s='Orders'}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#customers-notifications" role="tab">{l s='Customers'}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#messages-notifications" role="tab">{l s='Messages'}</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content">
        <div class="tab-pane active" id="orders-notifications" role="tabpanel"></div>
        <div class="tab-pane" id="customers-notifications" role="tabpanel"></div>
        <div class="tab-pane" id="messages-notifications" role="tabpanel"></div>
      </div>
    </div>
  </div>
</div>

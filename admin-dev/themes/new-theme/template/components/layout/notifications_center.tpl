<div class="notification-center dropdown">
  <div class="notification dropdown-toggle">
    <i class="material-icons">notifications_none</i>
    {* TODO: REFACTORING THIS IDENTIFIER *}
    <span id="orders_notif_value" class="count">0</span>
  </div>
  <div class="dropdown-menu dropdown-menu-right">
    <div class="notifications">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#orders-notifications" role="tab" id="orders-tab">{l s='Orders%s' sprintf='_nb_new_orders_'}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#customers-notifications" role="tab" id="customers-tab">{l s='Customers%s' sprintf='_nb_new_customers_'}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#messages-notifications" role="tab" id="messages-tab">{l s='Messages%s' sprintf='_nb_new_messages_'}</a>
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

<script type="text/html" id="order-notification-template">
  <a class="notif" href='order_url'>#_id_order_ - {l s="from"} <strong>_customer_name_</strong> (_iso_code_) - _carrier_ <strong class="pull-xs-right">_total_paid_</strong></a>
</script>

<script type="text/html" id="customer-notification-template">
  <a class="notif" href='customer_url'>#_id_customer_ - <strong>_customer_name_</strong> (_company_) - {l s="register"} <strong>_date_add_</strong></a>
</script>

<script type="text/html" id="message-notification-template">
  <a class="notif" href='message_url'><span class="message-notification-status _status_"><i class="material-icons">fiber_manual_record</i> _status_</span> - <strong>_customer_name_</strong> (_company_) - <i class="material-icons">access_time</i> _date_add_</a>
</script>

<div class="notification-center dropdown">
  <div class="notification dropdown-toggle">
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

<script type="text/html" id="order-notification-template">
  <a href='order_url'>#id_order - {l s="from"} customer_name (iso_code) - carrier total_paid</a>
</script>

<script type="text/html" id="customer-notification-template">
  <a href='customer_url'>#id_customer - customer_name (company) - {l s="register"} date_add</a>
</script>

<script type="text/html" id="message-notification-template">
  <a href='message_url'>status - customer_name (company) - date_add</a>
</script>

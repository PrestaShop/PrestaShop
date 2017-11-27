{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div id="notif" class="notification-center dropdown">
  <div class="notification js-notification dropdown-toggle" data-toggle="dropdown">
    <i class="material-icons">notifications_none</i>
    <span id="notifications-total" class="count hide">0</span>
  </div>
  <div class="dropdown-menu dropdown-menu-right js-notifs_dropdown">
    <div class="notifications">
      <ul class="nav nav-tabs" role="tablist">
        {$active = "active"}
        {if $show_new_orders}
          <li class="nav-item">
            <a
              class="nav-link {$active}"
              id="orders-tab"
              data-toggle="tab"
              data-type="order"
              href="#orders-notifications"
              role="tab"
            >
              {l s='Orders[1][/1]' html=true sprintf=['[1]' => '<span id="_nb_new_orders_">', '[/1]' => '</span>'] d='Admin.Navigation.Notification'}
            </a>
          </li>
          {$active = ""}
        {/if}
        {if $show_new_customers}
          <li class="nav-item">
            <a
              class="nav-link {$active}"
              id="customers-tab"
              data-toggle="tab"
              data-type="customer"
              href="#customers-notifications"
              role="tab"
            >
              {l s='Customers[1][/1]' html=true sprintf=['[1]' => '<span id="_nb_new_customers_">', '[/1]' => '</span>'] d='Admin.Navigation.Notification'}
            </a>
          </li>
          {$active = ""}
        {/if}
        {if $show_new_messages}
          <li class="nav-item">
            <a
              class="nav-link {$active}"
              id="messages-tab"
              data-toggle="tab"
              data-type="customer_message"
              href="#messages-notifications"
              role="tab"
            >
              {l s='Messages[1][/1]' html=true sprintf=['[1]' => '<span id="_nb_new_messages_">', '[/1]' => '</span>'] d='Admin.Navigation.Notification'}
            </a>
          </li>
          {$active = ""}
        {/if}
      </ul>

      <!-- Tab panes -->
      <div class="tab-content">
        {$active = "active"}
        {if $show_new_orders}
          <div class="tab-pane {$active} empty" id="orders-notifications" role="tabpanel">
            <p class="no-notification">
              {l s='No new order for now :(' d='Admin.Navigation.Notification'}<br>
              {$no_order_tip}
            </p>
            <div class="notification-elements"></div>
          </div>
          {$active = ""}
        {/if}
        {if $show_new_customers}
          <div class="tab-pane {$active} empty" id="customers-notifications" role="tabpanel">
            <p class="no-notification">
              {l s='No new customer for now :(' d='Admin.Navigation.Notification'}<br>
              {$no_customer_tip}
            </p>
            <div class="notification-elements"></div>
          </div>
          {$active = ""}
        {/if}
        {if $show_new_messages}
          <div class="tab-pane {$active} empty" id="messages-notifications" role="tabpanel">
            <p class="no-notification">
              {l s='No new message for now.' d='Admin.Navigation.Notification'}<br>
              {$no_customer_message_tip}
            </p>
            <div class="notification-elements"></div>
          </div>
          {$active = ""}
        {/if}
      </div>
    </div>
  </div>
</div>

{if $show_new_orders}
  <script type="text/html" id="order-notification-template">
    <a class="notif" href='order_url'>
      #_id_order_ -
      {l s='from' d='Admin.Navigation.Notification'} <strong>_customer_name_</strong> (_iso_code_)_carrier_
      <strong class="float-sm-right">_total_paid_</strong>
    </a>
  </script>
{/if}

{if $show_new_customers}
  <script type="text/html" id="customer-notification-template">
    <a class="notif" href='customer_url'>
      #_id_customer_ - <strong>_customer_name_</strong>_company_ - {l s='registered' d='Admin.Navigation.Notification'} <strong>_date_add_</strong>
    </a>
  </script>
{/if}

{if $show_new_messages}
  <script type="text/html" id="message-notification-template">
    <a class="notif" href='message_url'>
    <span class="message-notification-status _status_">
      <i class="material-icons">fiber_manual_record</i> _status_
    </span>
      - <strong>_customer_name_</strong> (_company_) - <i class="material-icons">access_time</i> _date_add_
    </a>
  </script>
{/if}

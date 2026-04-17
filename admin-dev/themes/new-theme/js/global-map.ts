/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default {
  navBar: {
    menuItems: '.main-menu .link-levelone.has_submenu.ul-open',
    menuItemLink: '.nav-bar li.link-levelone.has_submenu > a',
    menuArrow:
      '.nav-bar li.link-levelone.has_submenu a > i.material-icons.sub-tabs-arrow',
    levelOneOpenedList: '.nav-bar li.link-levelone.has_submenu.ul-open',
    levelOneOpenedSubmenu:
      '.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu',
  },
  notifications: {
    ordersNotifications: '#orders-notifications',
    orderNotificationTemplate: '#order-notification-template',
    customersNotifications: '#customers-notifications',
    customerNotificationTemplate: '#customer-notification-template',
    messagesNotifications: '#messages-notifications',
    messageNotificationTemplate: '#message-notification-template',
    total: '#notifications-total',
    element: '.notification-elements',
  },
};

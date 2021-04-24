/**
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
 */

/**
 * Toggle a class on $mainMenu after the end of an event (transition, animation...)
 * @param {jQuery element} $navBar - The navbar item which get a css transition property.
 * @param {jQuery element} $mainMenu - The menu inside the $navBar element.
 * @param {string} endTransitionEvent - The name of the event.
 * @param {jQuery element} $body - The body of the page.
 * @method showNavBarContent - Toggle the class based on event and if body got a class.
 * @method toggle - Add the listener if there is no transition launched yet.
 * @return {Object} The object with methods wich permit to toggle on specific event.
*/
function NavbarTransitionHandler($navBar, $mainMenu, endTransitionEvent, $body) {
  this.$body = $body;
  this.transitionFired = false;
  this.$navBar = $navBar.get(0);
  this.$mainMenu = $mainMenu;
  this.endTransitionEvent = endTransitionEvent;

  this.showNavBarContent = (event) => {
    if (event.propertyName !== 'width') {
      return;
    }

    this.$navBar.removeEventListener(this.endTransitionEvent, this.showNavBarContent);
    const isSidebarClosed = this.$body.hasClass('page-sidebar-closed');
    this.$mainMenu.toggleClass('sidebar-closed', isSidebarClosed);
    this.transitionFired = false;
  };

  this.toggle = () => {
    if (!this.transitionFired) {
      this.$navBar.addEventListener(this.endTransitionEvent, this.showNavBarContent.bind(this));
    } else {
      this.$navBar.removeEventListener(this.endTransitionEvent, this.showNavBarContent);
    }

    this.transitionFired = !this.transitionFired;
  };
}

export default NavbarTransitionHandler;

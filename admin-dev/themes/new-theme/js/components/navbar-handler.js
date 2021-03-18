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
 * This component watches a navigation bar and is able to link it alternative links, and automatic switch
 * on page load.
 *
 * You can add button with class tab-link when they are clicked the tab target is fetched
 * from the button's data property targetTab (so data-target-tab), it then search for a tab
 * which targets matches in the navbar and simulates a click on it.
 *
 * Alternatively it also checks on page load if a hash matches a tab and activates it if one is found,
 * and of course the hash is kept in sync when the navbar or alternative links are used.
 */
export default class NavbarHandler {
  constructor($navigationContainer, tabPrefix) {
    // We use a tab prefix for hastag so that on reload the page doesn't auto scroll to the anchored element
    this.tabPrefix = tabPrefix || 'tab-';
    this.$navigationContainer = $navigationContainer;

    this.watchNavbar();
    this.watchTabLinks();
    this.switchOnPageLoad();
  }

  switchToTarget(target) {
    if (!target) {
      return;
    }

    const matchingTabs = $(`[href="${target}"]`, this.$navigationContainer);

    if (matchingTabs.length <= 0) {
      return;
    }

    const tabLink = matchingTabs.first();
    this.switchToTab(tabLink);
  }

  switchToTab(tab) {
    tab.click();
    this.updateBrowserHash(tab.attr('href'));
  }

  updateBrowserHash(target) {
    const hashName = target.replace('#', `#${this.tabPrefix}`);

    if (window.history.pushState) {
      window.history.pushState(null, null, hashName);
    } else {
      window.location.hash = hashName;
    }
  }

  watchNavbar() {
    $(this.$navigationContainer).on('shown.bs.tab', (event) => {
      if (event.target.hash) {
        this.updateBrowserHash(event.target.hash);
      }
    });
  }

  watchTabLinks() {
    $('.tab-link').click((event) => {
      const target = $(event.target).data('targetTab');

      if (!target) {
        return;
      }

      this.switchToTarget(`#${target}`);
    });
  }

  switchOnPageLoad() {
    const errorTabs = $('.has-error', this.$navigationContainer);

    if (errorTabs.length) {
      const errorTab = $('a[role="tab"]', errorTabs.first()).first();
      this.switchToTab(errorTab);
    } else {
      const {hash} = document.location;
      const target = hash.replace(`#${this.tabPrefix}`, '#');
      this.switchToTarget(target);
    }
  }
}

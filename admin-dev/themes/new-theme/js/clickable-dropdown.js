/*
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
 *
 */

/**
 * By default, bootstrap dropdowns close down when the user clicks anywhere.
 * This plugin allows clicking inside the dropdown menu while keeping it open.
 * In order to make a dropdown behave like this, simply add the class "dropdown-clickable" to its parent element.
 */
(($) => {

  $.fn.clickableDropdown = function clickableDropdown() {

    $(document).on('click', '.dropdown-clickable .dropdown-menu', (e) => {
      e.stopPropagation();
    });

    return this;
  };

  // hook up the plugin
  $(function initClickableDropdown() {
    $(document).clickableDropdown();
  });
})(window.$);

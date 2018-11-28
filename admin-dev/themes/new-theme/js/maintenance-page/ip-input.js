/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const $ = window.$;
const IpInput = {};

IpInput.addRemoteAddr = (event) => {
  const input = $(event.target).prev('input');
  const inputValue = input.val() || "";
  const ip = event.target.dataset.ip || "";
  if (inputValue.length > 0) {
    if (input.val().indexOf(ip) < 0) {
      input.val(input.val() + ',' + ip);
    }
  } else {
    input.val(ip);
  }
};

IpInput.init = () => {
    $("body").on("click", '.add_ip_button', IpInput.addRemoteAddr);
};

export default IpInput;

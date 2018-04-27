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

class TermsAndConditionsOptionHandler {
  constructor() {
    this.handle();

    $('input[name="form[general][enable_tos]"]').on('change', () => this.handle());
  }

  handle() {
    const tosEnabledVal = $('input[name="form[general][enable_tos]"]:checked').val();
    const isTosEnabled = parseInt(tosEnabledVal);

    this.handleTermsAndConditionsCmsSelect(isTosEnabled);
  }

  /**
   * If terms and conditions option is disabled, then terms and conditions
   * cms select must be disabled.
   *
   * @param {int} isTosEnabled
   */
  handleTermsAndConditionsCmsSelect(isTosEnabled) {
    const tosCmsSelect = $('#form_general_tos_cms_id');

    if (isTosEnabled) {
        tosCmsSelect.removeAttr('disabled');
    } else {
        tosCmsSelect.attr('disabled', 'disabled');
    }
  }
}

export default TermsAndConditionsOptionHandler;

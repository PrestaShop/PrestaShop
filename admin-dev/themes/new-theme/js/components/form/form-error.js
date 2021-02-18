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
 /

const {$} = window;

/** Fixed error design where error appears at the top of the form instead of next to one of the fields */
export default class FormError {
  constructor() {
    const formErrorContainer = $('form > .form-errors-container');
    if (formErrorContainer.find('.d-inline-block').length > 0) {
      formErrorContainer.addClass('alert alert-danger');
      formErrorContainer.find('.text-danger').removeClass('text-danger');
      formErrorContainer.find('.form-error-icon-container').remove();
    }
  }
}

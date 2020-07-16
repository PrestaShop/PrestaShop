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

const $ = window.$;

/**
 * class TaggableField is responsible for providing functionality from bootstrap-tokenfield plugin.
 * It allows to have taggable fields which are split in separate blocks once you click enter. Values originally saved
 * in comma split strings.
 */
export default class TaggableField {
  /**
   * @param {string} tokenFieldSelector -  a selector which is used within jQuery object.
   * @param {object} options - extends basic tokenField behavior with additional options such as minLength, delimiter,
   * allow to add token on focus out action. See bootstrap-tokenfield docs for more information.
   */
  constructor({tokenFieldSelector, options = {}}) {
    $(tokenFieldSelector).tokenfield(options);
  }
}

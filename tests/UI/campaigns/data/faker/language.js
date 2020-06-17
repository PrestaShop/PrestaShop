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
const faker = require('faker');

module.exports = class Language {
  constructor(LanguageToCreate = {}) {
    this.name = LanguageToCreate.name || `test_language_${faker.lorem.word()}`;
    this.isoCode = LanguageToCreate.isoCode;
    this.languageCode = LanguageToCreate.languageCode || this.isoCode;
    this.dateFormat = LanguageToCreate.dateFormat || 'Y-m-d';
    this.fullDateFormat = LanguageToCreate.fullDateFormat || 'Y-m-d H:i:s';
    this.isRtl = LanguageToCreate.isRtl === undefined ? false : LanguageToCreate.isRtl;
    this.status = LanguageToCreate.status === undefined ? true : LanguageToCreate.status;
    this.flag = LanguageToCreate.flag || `flag_${this.name}.png`;
    this.noPicture = LanguageToCreate.noPicture || `no_picture_${this.name}.png`;
  }
};

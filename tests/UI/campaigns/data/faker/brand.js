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

module.exports = class Brand {
  constructor(brandToCreate = {}) {
    this.name = brandToCreate.name || faker.company.companyName();
    this.logo = `${this.name.replace(/[^\w\s]/gi, '')}.png`;
    this.shortDescription = brandToCreate.shortDescription || faker.lorem.sentence();
    this.shortDescriptionFr = brandToCreate.shortDescriptionFr || this.shortDescription;
    this.description = brandToCreate.description || faker.lorem.sentence();
    this.descriptionFr = brandToCreate.descriptionFr || this.description;
    this.metaTitle = brandToCreate.metaTitle || this.name;
    this.metaTitleFr = brandToCreate.metaTitleFr || this.metaTitle;
    this.metaDescription = brandToCreate.metaDescription || faker.lorem.sentence();
    this.metaDescriptionFr = brandToCreate.metaDescriptionFr || this.metaDescription;
    this.metaKeywords = brandToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];
    this.metaKeywordsFr = brandToCreate.metaKeywordsFr || this.metaKeywords;
    this.enabled = brandToCreate.enabled === undefined ? true : brandToCreate.enabled;
    this.addresses = brandToCreate.addresses || 0;
    this.products = brandToCreate.products || 0;
  }
};

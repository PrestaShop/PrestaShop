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
require('module-alias/register');
const {expect} = require('chai');
const testContext = require('@utils/testContext');
const loginPage = require('@pages/BO/login');
const dashboardPage = require('@pages/BO/dashboard');

module.exports = {
  async loginBO(mochaContext, page) {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginBO');
    await loginPage.goTo(page, global.BO.URL);
    await loginPage.login(page, global.BO.EMAIL, global.BO.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await dashboardPage.closeOnboardingModal(page);
  },

  async logoutBO(mochaContext, page) {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutBO');
    await dashboardPage.logoutBO(page);
    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  },
};

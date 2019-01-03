/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const chai = require('chai');
chai.use(require('chai-string'));
require('../test/globals.webdriverio');
global.expect = chai.expect;

const takeScreenshot = err => this.client.takeScreenshot().then(() => {
  throw err;
});

global.test = (name, instructions) => it(name, () => {
  return instructions().catch(takeScreenshot);
});

global.scenario = (name, tests, clientName, close = false) =>
  describe(name, () => {
    const PrestClient = require("./clients/" + clientName);
    const client = new PrestClient();
    before(() => this.client = client);
    tests(client);
    if (close) {
      after(() => this.client.close());
    }
  });

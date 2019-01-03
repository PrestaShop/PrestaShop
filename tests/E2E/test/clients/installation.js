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
var CommonClient = require('../../../E2E/test/clients/common_client');
const exec = require('child_process').exec;
var path = require('path');
class Installation extends CommonClient {

    setNameInput(selector, data) {
        return this.client
            .waitForVisible(selector, 90000)
            .pause(2000)
            .setValue(selector, data)
    }

    goToTheNextPage(selector) {
        return this.client
            .pause(2000)
            .waitForVisibleAndClick(selector, 90000)
    }

    dataBaseCreation(selector) {
        return this.client
            .waitForVisible(selector, 90000)
            .pause(2000)
            .click(selector)
    }

    WaitForDownload(selector) {
        return this.client
            .pause(150000)
    }

    renameFolders(rcTarget) {
        const renameAdmin = exec(' mv ' + rcTarget + 'admin' + ' ' + rcTarget + 'admin-dev',
            (error, stdout, stderr) => {
                if (error !== null) {
                    console.log(`exec error: ${error}`);
                }
            });
        const renameInstall = exec(' mv ' + rcTarget + 'install' + ' ' + rcTarget + 'install-dev',
            (error, stdout, stderr) => {
                if (error !== null) {
                    console.log(`exec error: ${error}`);
                }
            });
        return this.client
            .pause(2000)
    }

    copyFileToAutoUpgrade(downloadsFolderPath, filename, rcTarget) {
        const child = exec(' cp ' + downloadsFolderPath + filename + ' ' + rcTarget,
            (error, stdout, stderr) => {
                if (error !== null) {
                    console.log(`exec error: ${error}`);
                }
            });
        return this.client
            .pause(3000)
            .refresh();
    }

    getRCName(chaine) {
        global.filename = chaine.split('/')[chaine.split('/').length - 1];
        return this.client.pause(1000)
    }
}

module.exports = Installation;

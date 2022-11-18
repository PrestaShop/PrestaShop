// Import utils
// import helper from '/var/www/html/PrestaShop/tests/UI/utils/helpers';
import helper from '@utils/helpers';

// Import test context
import testContext from '/var/www/html/PrestaShop/tests/UI/utils/testContext';

// Import common tests
import loginCommon from '/var/www/html/PrestaShop/tests/UI/commonTests/BO/loginBO';

// Import pages
import loginPage from '/var/www/html/PrestaShop/tests/UI/pages/BO/login/index';

const baseContext: string = 'functional_BO_login_logout';

// Import expect from chai
import {expect} from 'chai';

import type {BrowserContext, Page} from 'playwright';

/*
Pre-condition
- Lognn to BO
Scenario:
- Logout from BO
 */
describe('BO - logout : log out from BO', async () => {
    let browserContext: BrowserContext;
    let page: Page;

    // before and after functions
    before(async function () {
        browserContext = await helper.createBrowserContext(this.browser);
        page = await helper.newTab(browserContext);
    });

    after(async () => {
        await helper.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
    });
});
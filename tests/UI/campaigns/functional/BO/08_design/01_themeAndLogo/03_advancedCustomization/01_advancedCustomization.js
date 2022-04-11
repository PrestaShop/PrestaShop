require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const advancedCustomizationPage = require('@pages/BO/themeAndLogo/advancedCustomization');

const baseContext = 'functional_BO_design_themeAndLogo_advancedCustomization';

let browserContext;
let page;

require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');
const FOBasePage = require('@pages/FO/FObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const HomePage = require('@pages/FO/home');
const InstallPage = require('@pages/Install/install');

module.exports =  async function initBasicPages(context = 'BO', pagesObjects, page) {
  let baseObject;
  switch(context) {
    case 'BO':
      baseObject = {
        boBasePage: new BOBasePage(page),
        loginPage: new LoginPage(page),
        dashboardPage: new DashboardPage(page),
      };
      break;
    case 'FO':
      baseObject = {
        foBasePage: new FOBasePage(page),
        homePage: new HomePage(page),
      };
      break;
    case 'Install':
      baseObject = {
        installPage: new InstallPage(page),
        homePage: new HomePage(page),
      };
      break;
    case 'BO + FO':
      baseObject = {
        boBasePage: new BOBasePage(page),
        loginPage: new LoginPage(page),
        dashboardPage: new DashboardPage(page),
        foBasePage: new FOBasePage(page),
        homePage: new HomePage(page),
      };
      break;
    default:
      // Do nothing
  }
  return {
    ...baseObject,
    ...pagesObjects,
  };
};

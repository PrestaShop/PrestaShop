require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');
const FOBasePage = require('@pages/FO/FObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const HomePage = require('@pages/FO/home');
const InstallPage = require('@pages/Install/install');

module.exports = async function init(context, pagesObjects, page) {
  let returnItems = {};

  switch (context) {
    case 'BO':
      returnItems = {
        boBasePage: new BOBasePage(page),
        loginPage: new LoginPage(page),
        dashboardPage: new DashboardPage(page),
      };
      break;
    case 'FO':
      returnItems = {
        foBasePage: new FOBasePage(page),
        homePage: new HomePage(page),
      };
      break;
    case 'Install':
      returnItems = {
        installPage: new InstallPage(page),
        homePage: new HomePage(page),
      };
      break;
    case 'All':
      returnItems = {
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

  Object.keys(pagesObjects).forEach((item) => {
    const returnItem = {
      [item]: new pagesObjects[item](page),
    };
    returnItems = {
      ...returnItems,
      ...returnItem,
    };
  });
  return returnItems;
};

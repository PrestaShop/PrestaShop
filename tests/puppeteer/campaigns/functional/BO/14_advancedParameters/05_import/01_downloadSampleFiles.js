require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ImportPage = require('@pages/BO/advancedParameters/import');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_import_downloadSampleFiles';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    importPage: new ImportPage(page),
  };
};

describe('Download import sample csv files', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login from BO and go to webservice page
  loginCommon.loginBO();

  it('should go to import page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToImportPage', baseContext);
    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.advancedParametersLink,
      this.pageObjects.dashboardPage.importLink,
    );
    await this.pageObjects.importPage.closeSfToolBar();
    const pageTitle = await this.pageObjects.importPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.importPage.pageTitle);
  });

  const sampleFiles = [
    {
      args:
        {
          type: 'categories_import',
          textToCheck: 'Category ID;Active (0/1);Name *;Parent category;Root category (0/1);Description;Meta title;'
            + 'Meta keywords;Meta description;URL rewritten;Image URL',
        },
    },
    {
      args:
        {
          type: 'products_import',
          textToCheck: 'Product ID;Active (0/1);Name *;Categories (x,y,z...);Price tax excluded;Tax rules ID;'
            + 'Wholesale price;On sale (0/1);Discount amount;Discount percent;Discount from (yyyy-mm-dd);'
            + 'Discount to (yyyy-mm-dd);Reference #;Supplier reference #;Supplier;Manufacturer;EAN13;UPC;Ecotax;'
            + 'Width;Height;Depth;Weight;Delivery time of in-stock products;'
            + 'Delivery time of out-of-stock products with allowed',
        },
    },
    {
      args:
        {
          type: 'combinations_import',
          textToCheck: 'Product ID*;Attribute (Name:Type:Position)*;Value (Value:Position)*;'
            + 'Supplier reference;Reference;EAN13;UPC;Wholesale price;Impact on price;Ecotax;Quantity;'
            + 'Minimal quantity;Low stock level;Impact on',
        },
    },
    {
      args:
        {
          type: 'customers_import',
          textToCheck: 'Customer ID;Active (0/1);Titles ID (Mr = 1, Ms = 2, else 0);Email *;'
            + 'Password *;Birthday (yyyy-mm-dd);Last Name *;First Name *;Newsletter (0/1);Opt-in (0/1);'
            + 'Registration date (yyyy-mm-dd);Groups',
        },
    },
    {
      args:
        {
          type: 'addresses_import',
          textToCheck: 'Address ID;Alias*;Active (0/1);Customer e-mail*;Customer ID;'
            + 'Manufacturer;Supplier;Company;Lastname*;Firstname*;Address 1*;Address 2;Zipcode*;City*;'
            + 'Country*;State;Other;Phone;Mobile Phone;VAT number;DNI',
        },
    },
    {
      args:
        {
          type: 'manufacturers_import',
          textToCheck: 'Manufacturer ID;Active (0/1);Name *;Description;Short description;'
            + 'Meta title;Meta keywords;Meta description;Image URL',
        },
    },
    {
      args:
        {
          type: 'suppliers_import',
          textToCheck: 'Supplier ID;Active (0/1);Name *;Description;Short description;Meta title;'
            + 'Meta keywords;Meta description;Image URL',
        },
    },
    {
      args:
        {
          type: 'alias_import',
          textToCheck: 'Alias ID;Alias *;Search *;Active (0/1)',
        },
    },
    {
      args:
        {
          type: 'store_contacts',
          textToCheck: 'Store ID;active;name;address1;address2;postcode;state;city;country;latitude;'
            + 'longitude;phone;fax;email;note;hours;image',
        },
    },
  ];

  sampleFiles.forEach((sampleFile) => {
    describe(`Download and check text for ${sampleFile.args.type} sample file`, async () => {
      it(`should download ${sampleFile.args.type} sample file`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${sampleFile.args.type}DownloadFile`, baseContext);
        await this.pageObjects.importPage.downloadSampleFile(sampleFile.args.type);
        const doesFileExist = await files.doesFileExist(`${sampleFile.args.type}.csv`);
        await expect(doesFileExist, `${sampleFile.args.type} sample file was not downloaded`).to.be.true;
      });

      it(`should check ${sampleFile.args.type} sample text file`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${sampleFile.args.type}checkTextFile`, baseContext);
        const textExist = await files.isTextInFile(`${sampleFile.args.type}.csv`, sampleFile.args.textToCheck);
        await expect(textExist, `Text was not found in ${sampleFile.args.type} sample file`).to.be.true;
      });

      // Delete file downloaded after checking it
      after(() => files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${sampleFile.args.type}.csv`));
    });
  });
});

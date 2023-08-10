// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import importPage from '@pages/BO/advancedParameters/import';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_import_downloadSampleFiles';

describe('BO - Advanced Parameters - Import : Download sample csv files', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

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

  it('should go to \'Advanced Parameters > Import\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToImportPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.importLink,
    );
    await importPage.closeSfToolBar(page);

    const pageTitle = await importPage.getPageTitle(page);
    await expect(pageTitle).to.contains(importPage.pageTitle);
  });

  const sampleFiles = [
    {
      args:
        {
          type: 'categories_import',
          textToCheck: 'Category ID;Active (0/1);Name *;Parent category;Root category (0/1);Description;Meta title;'
            + 'Meta description;URL rewritten;Image URL',
        },
    },
    {
      args:
        {
          type: 'products_import',
          textToCheck: 'Product ID;Active (0/1);Name *;Categories (x,y,z...);Price tax excluded;Tax rules ID;'
            + 'Wholesale price;On sale (0/1);Discount amount;Discount percent;Discount from (yyyy-mm-dd);'
            + 'Discount to (yyyy-mm-dd);Reference #;Supplier reference #;Supplier;Manufacturer;EAN13;UPC;MPN;Ecotax;'
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
          type: 'brands_import',
          textToCheck: 'ID;Active (0/1);Name *;Description;Short description;'
            + 'Meta title;Meta description;Image URL',
        },
    },
    {
      args:
        {
          type: 'suppliers_import',
          textToCheck: 'Supplier ID;Active (0/1);Name *;Description;Meta title;'
            + 'Meta description;Image URL',
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

        filePath = await importPage.downloadSampleFile(page, sampleFile.args.type);

        const doesFileExist = await files.doesFileExist(filePath);
        await expect(doesFileExist, `${sampleFile.args.type} sample file was not downloaded`).to.be.true;
      });

      it(`should check ${sampleFile.args.type} sample text file`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${sampleFile.args.type}checkTextFile`, baseContext);

        const textExist = await files.isTextInFile(filePath, sampleFile.args.textToCheck);
        await expect(textExist, `Text was not found in ${sampleFile.args.type} sample file`).to.be.true;
      });
    });
  });
});

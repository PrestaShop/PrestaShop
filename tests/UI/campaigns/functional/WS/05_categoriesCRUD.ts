import testContext from '@utils/testContext';
import {expect} from 'chai';

import getCategoryXml from '@data/xml/category';
import {addWebserviceKey, removeWebserviceKey, setWebserviceStatus} from '@commonTests/BO/advancedParameters/ws';
import categoryXml from '@webservices/category/categoryXml';
import CategoryWS from '@webservices/category/categoryWs';

import {
  type APIRequestContext,
  type APIResponse,
  boCategoriesPage,
  boCategoriesCreatePage,
  boDashboardPage,
  boLoginPage,
  boWebservicesPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
  utilsXML,
  type WebservicePermission,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_WS_categoriesCRUD';

describe('WS - Categories : CRUD', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let wsKey: string = '';
  let authorization: string = '';

  const wsKeyDescription: string = 'Webservice Key - Categories';
  const wsKeyPermissions: WebservicePermission[] = [
    {
      resource: 'categories',
      methods: ['all'],
    },
  ];
  const xmlCreate: string = getCategoryXml();
  let xmlUpdate: string;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    apiContext = await utilsPlaywright.createAPIContext(global.FO.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Enable webservice
  setWebserviceStatus(true, `${baseContext}_preTest_1`);

  // Create a new webservice key
  addWebserviceKey(wsKeyDescription, wsKeyPermissions, `${baseContext}_preTest_2`);

  describe('Categories : CRUD', () => {
    let categoryNodeID: string | null = '';

    describe('Fetch the Webservice Key', () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Advanced Parameters > Webservice\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.webserviceLink,
        );
        await boWebservicesPage.closeSfToolBar(page);

        const pageTitle = await boWebservicesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boWebservicesPage.pageTitle);
      });

      it('should filter list by key description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeDelete', baseContext);

        await boWebservicesPage.resetAndGetNumberOfLines(page);
        await boWebservicesPage.filterWebserviceTable(page, 'input', 'description', wsKeyDescription);

        const description = await boWebservicesPage.getTextColumnFromTable(page, 1, 'description');
        expect(description).to.contains(wsKeyDescription);

        wsKey = await boWebservicesPage.getTextColumnFromTable(page, 1, 'key');
        authorization = `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`;
        expect(wsKey).to.not.have.lengthOf(0);
      });
    });

    describe(`Endpoint : ${CategoryWS.endpoint} - Schema : Blank`, () => {
      let apiResponse: APIResponse;
      let xmlResponse: string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankStatus', baseContext);

        apiResponse = await CategoryWS.getBlank(apiContext, authorization);
        expect(apiResponse.status()).to.eq(200);
      });

      it('should check that the blank XML can be parsed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankValid', baseContext);

        xmlResponse = await apiResponse.text();
        expect(utilsXML.isValid(xmlResponse)).to.eq(true);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankRootNode', baseContext);

        expect(categoryXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNode', baseContext);

        const rootNodes = categoryXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('category');
      });

      it('should check each node name, attributes has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNodes', baseContext);

        const nodes = categoryXml.getCategoryNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];

          // Attributes
          const nodeAttributes: NamedNodeMap = node.attributes;
          expect(nodeAttributes.length).to.be.eq(0);

          // Empty value
          const isEmptyNode: boolean = utilsXML.isEmpty(node);
          expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.eq(true);
        }
      });
    });

    describe(`Endpoint : ${CategoryWS.endpoint} - Schema : Synopsis`, () => {
      let apiResponse: APIResponse;
      let xmlResponse: string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisStatus', baseContext);

        apiResponse = await CategoryWS.getSynopsis(apiContext, authorization);
        expect(apiResponse.status()).to.eq(200);
      });

      it('should check that the synopsis XML can be parsed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisValid', baseContext);

        xmlResponse = await apiResponse.text();
        expect(utilsXML.isValid(xmlResponse)).to.eq(true);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisRootNode', baseContext);

        expect(categoryXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNode', baseContext);

        const rootNodes = categoryXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('category');
      });

      it('should check each node has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNodes', baseContext);

        const nodes = categoryXml.getCategoryNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];
          const nodeAttributes: NamedNodeMap = node.attributes;

          if (nodeAttributes.length > 0) {
            expect(nodeAttributes[nodeAttributes.length - 1].nodeName)
              .to.be.oneOf(['format', 'readOnly', 'read_only', 'notFilterable', 'required']);
          }

          const isEmptyNode = utilsXML.isEmpty(node);
          expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.eq(true);
        }
      });
    });

    describe(`Endpoint : ${CategoryWS.endpoint} - Method : GET`, () => {
      let apiResponse: APIResponse;
      let xmlResponse: string;
      let categoriesNode: Element[];

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetStatus1', baseContext);

        apiResponse = await CategoryWS.getAll(apiContext, authorization);
        expect(apiResponse.status()).to.eq(200);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetRootNode1', baseContext);

        xmlResponse = await apiResponse.text();
        expect(categoryXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNodeNumber1', baseContext);

        const rootNodes = categoryXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('categories');
      });

      it('should check number of nodes under categories node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNumberOfNodes1', baseContext);

        categoriesNode = categoryXml.getAllCategories(xmlResponse);
        expect(categoriesNode.length).to.be.gt(0);
      });

      it('should check each node name, attributes and xlink:href', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetCheckAll1', baseContext);

        for (let c: number = 0; c < categoriesNode.length; c++) {
          const categoryNode: Element = categoriesNode[c];
          expect(categoryNode.nodeName).to.be.eq('category');

          const attrs: NamedNodeMap = categoryNode.attributes;
          expect(attrs.length).to.be.eq(2);

          // Attribute : id
          expect(attrs[0].nodeName).to.be.eq('id');
          const id = attrs[0].nodeValue as string;
          expect(id).to.be.eq(parseInt(id, 10).toString());

          // Attribute : xlink:href
          expect(attrs[1].nodeName).to.be.eq('xlink:href');
          expect(attrs[1].nodeValue).to.be.a('string');
        }
      });
    });

    describe(`Endpoint : ${CategoryWS.endpoint} - Method : POST`, () => {
      describe(`Endpoint : ${CategoryWS.endpoint} - Method : POST - Add Category`, () => {
        let apiResponse: APIResponse;
        let xmlResponse: string;

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostStatus1', baseContext);

          apiResponse = await CategoryWS.add(apiContext, authorization, xmlCreate);
          expect(apiResponse.status()).to.eq(201);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(categoryXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostNodeNumber1', baseContext);

          const rootNodes = categoryXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('category');
        });

        it('should check id of the category', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostCheckId1', baseContext);

          categoryNodeID = categoryXml.getAttributeValue(xmlResponse, 'id');
          expect(categoryNodeID).to.be.a('string');
          expect(categoryNodeID).to.be.eq(parseInt(categoryNodeID as string, 10).toString());
        });
      });

      describe(`Endpoint : ${CategoryWS.endpoint}/{id} - Method : POST - Check with WS`, () => {
        let apiResponse: APIResponse;
        let xmlResponse: string;
        let categoriesNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDStatus', baseContext);

          apiResponse = await CategoryWS.getById(apiContext, authorization, categoryNodeID as string);
          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(categoryXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNodeNumber1', baseContext);

          const rootNodes = categoryXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('category');
        });

        it('should check number of nodes under category node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNumberOfNodes1', baseContext);

          categoriesNodes = categoryXml.getCategoryNodes(xmlResponse);
          expect(categoriesNodes.length).to.be.gt(0);
        });

        it('should check each node attribute', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDCheckAll', baseContext);

          const ignoredFields: string[] = [
            'level_depth',
            'nb_products_recursive',
            'position',
            'date_add',
            'date_upd',
            'associations',
            'id_shop_default',
            'is_root_category',
            'redirect_type',
            'id_type_redirected',
          ];

          for (let o: number = 0; o < categoriesNodes.length; o++) {
            const oNode: Element = categoriesNodes[o];

            // Skip the calculed node
            if (!ignoredFields.includes(oNode.nodeName)) {
              if (oNode.nodeName === 'id') {
                expect(oNode.textContent).to.be.eq(categoryNodeID as string);
              } else if ([
                'name',
                'description',
                'additional_description',
                'link_rewrite',
                'meta_title',
                'meta_description',
                'meta_keywords',
              ].includes(oNode.nodeName)) {
                const objectNodeValueEN = categoryXml.getAttributeLangValue(xmlResponse, oNode.nodeName, '1');
                const createNodeValueEN = categoryXml.getAttributeLangValue(xmlCreate, oNode.nodeName, '1');
                const objectNodeValueFR = categoryXml.getAttributeLangValue(xmlResponse, oNode.nodeName, '2');
                const createNodeValueFR = categoryXml.getAttributeLangValue(xmlCreate, oNode.nodeName, '2');
                expect(objectNodeValueEN).to.be.eq(createNodeValueEN);
                expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
              } else {
                const objectNodeValue = categoryXml.getAttributeValue(xmlCreate, oNode.nodeName);
                expect(objectNodeValue).to.be.a('string');
                expect(oNode.textContent).to.be.eq(objectNodeValue);
              }
            }
          }
        });
      });

      describe(`Endpoint : ${CategoryWS.endpoint} - Method : POST - Check On BO`, () => {
        it('should go to \'Catalog > Categories\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.catalogParentLink,
            boDashboardPage.categoriesLink,
          );
          await boCategoriesPage.closeSfToolBar(page);

          const pageTitle = await boCategoriesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
        });

        it('should filter category by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPost', baseContext);

          const numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
          expect(numberOfCategories).to.be.above(0);

          await boCategoriesPage.filterCategories(page, 'input', 'id_category', categoryNodeID as string);

          const numberOfCategoriesAfterFilter = await boCategoriesPage.getNumberOfElementInGrid(page);
          expect(numberOfCategoriesAfterFilter).to.be.eq(1);

          const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category');
          expect(textColumn).to.contains(categoryNodeID as string);
        });

        it('should go to edit category page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditCategoryPageAfterPost', baseContext);

          await boCategoriesPage.goToEditCategoryPage(page, 1);

          const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleEdit);
        });

        it('should check category\'s name EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryNameEN2', baseContext);

          const xmlValueNameEn = categoryXml.getAttributeLangValue(xmlCreate, 'name', '1');
          const valueNameEn = await boCategoriesCreatePage.getValue(page, 'name');
          expect(valueNameEn).to.be.eq(xmlValueNameEn);
        });

        it('should check category\'s description EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryDescription1', baseContext);

          const xmlValueDescEn = categoryXml.getAttributeLangValue(xmlCreate, 'description', '1');
          const valueDescEn = await boCategoriesCreatePage.getValue(page, 'description');
          expect(valueDescEn).to.be.eq(xmlValueDescEn);
        });

        it('should check category\'s meta title EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryMetaTitle', baseContext);

          const xmlValueMetaTitleEn = categoryXml.getAttributeLangValue(xmlCreate, 'meta_title', '1');
          const valueMetaTitleEn = await boCategoriesCreatePage.getValue(page, 'metaTitle');
          expect(valueMetaTitleEn).to.be.eq(xmlValueMetaTitleEn);
        });

        it('should check category\'s meta description EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryDescriptionEN', baseContext);

          const xmlValueMetaDescEn = categoryXml.getAttributeLangValue(xmlCreate, 'meta_description', '1');
          const valueMetaDescEn = await boCategoriesCreatePage.getValue(page, 'metaDescription');
          expect(valueMetaDescEn).to.be.eq(xmlValueMetaDescEn);
        });

        it('should check category\'s active', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryActive2', baseContext);

          const xmlValueActive = categoryXml.getAttributeValue(xmlCreate, 'active');
          const valueActive = await boCategoriesCreatePage.getValue(page, 'active');
          expect(valueActive).to.be.eq(xmlValueActive);
        });

        it('should check category\'s name FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryNameFR1', baseContext);

          const xmlValueNameFr = categoryXml.getAttributeLangValue(xmlCreate, 'name', '2');
          const valueNameFr = await boCategoriesCreatePage.getValue(page, 'name', 'fr');
          expect(valueNameFr).to.be.eq(xmlValueNameFr);
        });

        it('should check category\'s description FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryDescriptionFR2', baseContext);

          const xmlValueDescEn = categoryXml.getAttributeLangValue(xmlCreate, 'description', '2');
          const valueDescEn = await boCategoriesCreatePage.getValue(page, 'description', 'fr');
          expect(valueDescEn).to.be.eq(xmlValueDescEn);
        });

        it('should check category\'s meta title FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryMetaTitleFR2', baseContext);

          const xmlValueMetaTitleEn = categoryXml.getAttributeLangValue(xmlCreate, 'meta_title', '2');
          const valueMetaTitleEn = await boCategoriesCreatePage.getValue(page, 'metaTitle', 'fr');
          expect(valueMetaTitleEn).to.be.eq(xmlValueMetaTitleEn);
        });

        it('should check category\'s meta description FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryDescriptionFR', baseContext);

          const xmlValueMetaDescEn = categoryXml.getAttributeLangValue(xmlCreate, 'meta_description', '2');
          const valueMetaDescEn = await boCategoriesCreatePage.getValue(page, 'metaDescription', 'fr');
          expect(valueMetaDescEn).to.be.eq(xmlValueMetaDescEn);
        });

        it('should go to \'Categories\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage2', baseContext);

          await boCategoriesCreatePage.clickOnBreadCrumbLink(page, 'categories');

          const pageTitle = await boCategoriesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPost', baseContext);

          const numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
          expect(numberOfCategories).to.be.above(0);
        });
      });
    });

    describe(`Endpoint : ${CategoryWS.endpoint} - Method : PUT`, () => {
      describe(`Endpoint : ${CategoryWS.endpoint} - Method : PUT - Update Category`, () => {
        let apiResponse: APIResponse;
        let xmlResponse: string;

        it(`should check response status of ${CategoryWS.endpoint}/{id}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutStatus1', baseContext);

          xmlUpdate = getCategoryXml(categoryNodeID as string);
          apiResponse = await CategoryWS.update(apiContext, authorization, categoryNodeID as string, xmlUpdate);
          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(categoryXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check the id of the category', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutCheckId1', baseContext);

          categoryNodeID = categoryXml.getAttributeValue(xmlResponse, 'id');
          expect(categoryNodeID).to.be.a('string');
          expect(categoryNodeID).to.be.eq(parseInt(categoryNodeID as string, 10).toString());
        });
      });

      describe(`Endpoint : ${CategoryWS.endpoint}/{id} - Method : PUT - Check with WS`, () => {
        let apiResponse: APIResponse;
        let xmlResponse: string;
        let categoriesNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutStatus2', baseContext);

          apiResponse = await CategoryWS.getById(apiContext, authorization, categoryNodeID as string);
          expect(apiResponse.status()).to.eq(200);
        });

        it('should check number of nodes under category node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNumberOfNodes1', baseContext);

          xmlResponse = await apiResponse.text();
          categoriesNodes = categoryXml.getCategoryNodes(xmlResponse);
          expect(categoriesNodes.length).to.be.gt(0);
        });

        it('should check each node id, name', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDCheckAll2', baseContext);

          const ignoredFields: string[] = [
            'level_depth',
            'nb_products_recursive',
            'position',
            'date_add',
            'date_upd',
            'associations',
            'id_shop_default',
            'is_root_category',
            'redirect_type',
            'id_type_redirected',
          ];

          for (let o: number = 0; o < categoriesNodes.length; o++) {
            const oNode: Element = categoriesNodes[o];

            if (!ignoredFields.includes(oNode.nodeName)) {
              if (oNode.nodeName === 'id') {
                expect(oNode.textContent).to.be.eq(categoryNodeID as string);
              } else if ([
                'name',
                'description',
                'additional_description',
                'link_rewrite',
                'meta_title',
                'meta_description',
                'meta_keywords',
              ].includes(oNode.nodeName)) {
                const objectNodeValueEN = categoryXml.getAttributeLangValue(xmlResponse, oNode.nodeName, '1');
                const createNodeValueEN = categoryXml.getAttributeLangValue(xmlUpdate, oNode.nodeName, '1');
                const objectNodeValueFR = categoryXml.getAttributeLangValue(xmlResponse, oNode.nodeName, '2');
                const createNodeValueFR = categoryXml.getAttributeLangValue(xmlUpdate, oNode.nodeName, '2');
                expect(objectNodeValueEN).to.be.eq(createNodeValueEN);
                expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
              } else {
                const objectNodeValue = categoryXml.getAttributeValue(xmlUpdate, oNode.nodeName);
                expect(objectNodeValue).to.be.a('string');
                expect(oNode.textContent).to.be.eq(objectNodeValue);
              }
            }
          }
        });
      });

      describe(`Endpoint : ${CategoryWS.endpoint} - Method : PUT - Check On BO`, () => {
        it('should go to \'Catalog > Categories\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage3', baseContext);

          await boCategoriesCreatePage.clickOnBreadCrumbLink(page, 'categories');

          const pageTitle = await boCategoriesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
        });

        it('should filter category by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPut', baseContext);

          await boCategoriesPage.resetFilter(page);
          await boCategoriesPage.filterCategories(page, 'input', 'id_category', categoryNodeID as string);

          const numberOfCategoriesAfterFilter = await boCategoriesPage.getNumberOfElementInGrid(page);
          expect(numberOfCategoriesAfterFilter).to.be.eq(1);
        });

        it('should go to edit category page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditCategoryPageAfterPut', baseContext);

          await boCategoriesPage.goToEditCategoryPage(page, 1);

          const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleEdit);
        });

        it('should check category\'s name EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryNameEN1', baseContext);

          const xmlValueNameEn = categoryXml.getAttributeLangValue(xmlUpdate, 'name', '1');
          const valueNameEn = await boCategoriesCreatePage.getValue(page, 'name');
          expect(valueNameEn).to.be.eq(xmlValueNameEn);
        });

        it('should check category\'s description EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryDescriptionEN1', baseContext);

          const xmlValueDescEn = categoryXml.getAttributeLangValue(xmlUpdate, 'description', '1');
          const valueDescEn = await boCategoriesCreatePage.getValue(page, 'description');
          expect(valueDescEn).to.be.eq(xmlValueDescEn);
        });

        it('should check category\'s meta title EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryMetaTitleEN', baseContext);

          const xmlValueMetaTitleEn = categoryXml.getAttributeLangValue(xmlUpdate, 'meta_title', '1');
          const valueMetaTitleEn = await boCategoriesCreatePage.getValue(page, 'metaTitle');
          expect(valueMetaTitleEn).to.be.eq(xmlValueMetaTitleEn);
        });

        it('should check category\'s meta description EN', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryMetaTitleEN2', baseContext);

          const xmlValueMetaDescEn = categoryXml.getAttributeLangValue(xmlUpdate, 'meta_description', '1');
          const valueMetaDescEn = await boCategoriesCreatePage.getValue(page, 'metaDescription');
          expect(valueMetaDescEn).to.be.eq(xmlValueMetaDescEn);
        });

        it('should check category\'s active', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryActive1', baseContext);

          const xmlValueActive = categoryXml.getAttributeValue(xmlUpdate, 'active');
          const valueActive = await boCategoriesCreatePage.getValue(page, 'active');
          expect(valueActive).to.be.eq(xmlValueActive);
        });

        it('should check category\'s name FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryNameFR2', baseContext);

          const xmlValueNameFr = categoryXml.getAttributeLangValue(xmlUpdate, 'name', '2');
          const valueNameFr = await boCategoriesCreatePage.getValue(page, 'name', 'fr');
          expect(valueNameFr).to.be.eq(xmlValueNameFr);
        });

        it('should check category\'s description FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryDescriptionFR3', baseContext);

          const xmlValueDescEn = categoryXml.getAttributeLangValue(xmlUpdate, 'description', '2');
          const valueDescEn = await boCategoriesCreatePage.getValue(page, 'description', 'fr');
          expect(valueDescEn).to.be.eq(xmlValueDescEn);
        });

        it('should check category\'s meta title FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryMetaTitleFR3', baseContext);

          const xmlValueMetaTitleEn = categoryXml.getAttributeLangValue(xmlUpdate, 'meta_title', '2');
          const valueMetaTitleEn = await boCategoriesCreatePage.getValue(page, 'metaTitle', 'fr');
          expect(valueMetaTitleEn).to.be.eq(xmlValueMetaTitleEn);
        });

        it('should check category\'s meta description FR', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryMetaDescriptionFR', baseContext);

          const xmlValueMetaDescEn = categoryXml.getAttributeLangValue(xmlUpdate, 'meta_description', '2');
          const valueMetaDescEn = await boCategoriesCreatePage.getValue(page, 'metaDescription', 'fr');
          expect(valueMetaDescEn).to.be.eq(xmlValueMetaDescEn);
        });

        it('should go to \'Categories\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage4', baseContext);

          await boCategoriesCreatePage.clickOnBreadCrumbLink(page, 'categories');

          const pageTitle = await boCategoriesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPut', baseContext);

          const numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
          expect(numberOfCategories).to.be.above(0);
        });
      });
    });

    describe(`Endpoint : ${CategoryWS.endpoint} - Method : DELETE`, () => {
      it(`should request the endpoint ${CategoryWS.endpoint}/{id} with method DELETE`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCategoriesMethodDelete', baseContext);

        const apiResponse = await CategoryWS.delete(apiContext, authorization, categoryNodeID as string);
        expect(apiResponse.status()).to.eq(200);
      });

      it(`should request the endpoint ${CategoryWS.endpoint}/{id} with method GET`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCategoriesIdMethodGetAfterDelete', baseContext);

        const apiResponse = await CategoryWS.getById(apiContext, authorization, categoryNodeID as string);
        expect(apiResponse.status()).to.eq(404);
      });

      it('should filter category by ID and check it no longer exists in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterDelete', baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(page, 'input', 'id_category', categoryNodeID as string);

        const numberOfCategoriesAfterFilter = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterFilter).to.be.eq(0);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

        const numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategories).to.be.above(0);
      });
    });
  });

  // Remove a new webservice key
  removeWebserviceKey(wsKeyDescription, `${baseContext}_postTest_1`);

  // Disable webservice
  setWebserviceStatus(false, `${baseContext}_postTest_2`);
});

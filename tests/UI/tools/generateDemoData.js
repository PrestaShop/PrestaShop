const fs = require('fs');
const parser = require('xml2json');
const {dirname, sep} = require('path');
const beautify = require('json-beautify');
const {exec} = require('child_process');

const argFixtures = process.argv.slice(2).toString();
const listFixtures = [
  'products',
];

if (!listFixtures.includes(argFixtures)) {
  console.error(
    `There are no generation for the fixture ${argFixtures}. Please use of these: ${listFixtures.join(', ')}.`,
  );
  return;
}

const dirPS = dirname(dirname(dirname(__dirname))) + sep;
const taxRateFR = 20;

// Fixtures for products
if (argFixtures === 'products') {
  const outputFile = 'data/demo/products1.js';
  const dataProduct = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/data/product.xml`),
  ));
  const dataProductEn = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/langs/en/data/product.xml`),
  ));
  const dataProductFr = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/langs/fr/data/product.xml`),
  ));
  const dataAttribute = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/data/attribute.xml`),
  ));
  const dataProductAttribute = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/data/product_attribute.xml`),
  ));
  const dataProductAttributeCombination = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/data/product_attribute_combination.xml`),
  ));
  const dataSpecificPrice = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/data/specific_price.xml`),
  ));
  const dataStockAvailable = JSON.parse(parser.toJson(
    fs.readFileSync(`${dirPS}install-dev/fixtures/fashion/data/stock_available.xml`),
  ));
  const getExternalData = function (data, identifier, value, attribute) {
    let returnValue = '';
    Object.keys(data).forEach((i) => {
      if (data[i][identifier] === value) {
        returnValue = data[i][attribute].toString().replace(/<[^>]*>?/gm, '').trim();
      }
    });
    return returnValue;
  };
  const getMultipleExternalData = function (data, identifier, value, attribute, key = false) {
    const returnValue = [];
    Object.keys(data).forEach((i) => {
      if (data[i][identifier] === value) {
        const dataValue = data[i][attribute].toString().replace(/<[^>]*>?/gm, '').trim();
        if (key) {
          returnValue[data[i][key]] = dataValue;
        } else {
          returnValue.push(dataValue);
        }
      }
    });
    return returnValue;
  };

  const exportData = {};
  Object.keys(dataProduct.entity_product.entities.product).forEach((i) => {
    const productItem = dataProduct.entity_product.entities.product[i];
    const productId = productItem.id;
    const productSPReduction = parseFloat(getExternalData(
      dataSpecificPrice.entity_specific_price.entities.specific_price, 'id_product', productId, 'reduction',
    ));

    const {reference} = productItem;
    const id = parseInt(i, 10) + 1;
    const category = productItem.id_category_default.replace('_', ' ');
    const price = parseFloat(productItem.price);
    const regularPrice = (price / 100) * (100 + taxRateFR);
    const weight = parseFloat(productItem.weight);
    const status = productItem.active === '1';
    const name = getExternalData(dataProductEn.entity_product.product, 'id', productId, 'name');
    const nameFR = getExternalData(dataProductFr.entity_product.product, 'id', productId, 'name');
    const description = getExternalData(dataProductEn.entity_product.product, 'id', productId, 'description');
    const shortDescription = getExternalData(
      dataProductEn.entity_product.product, 'id', productId, 'description_short',
    );
    const coverImage = `${getExternalData(dataProductEn.entity_product.product, 'id', productId, 'link_rewrite')}.jpg`;
    const thumbnailImage = `product_mini_${id}.jpg`;
    const discountPercentage = Number.isNaN(productSPReduction) ? null : productSPReduction * 100;
    const discount = Number.isNaN(productSPReduction) ? null : `${discountPercentage}%`;
    const finalPrice = parseFloat(parseFloat(
      (regularPrice / 100) * (100 - Number.isNaN(productSPReduction) ? 0 : discountPercentage),
    ).toFixed(2));
    // Stock
    const productSA = getMultipleExternalData(
      dataStockAvailable.entity_stock_available.entities.stock_available,
      'id_product',
      id.toString(),
      'quantity',
      'id_product_attribute',
    );
    let quantity = 0;
    productSA.forEach((v, k) => {
      quantity += (k === 0 ? 0 : parseInt(v, 10));
    });
    // Combinations
    const productPA = getMultipleExternalData(
      dataProductAttribute.entity_product_attribute.entities.product_attribute,
      'id_product',
      productId,
      'id',
    );
    let attributes = [];
    productPA.forEach((v) => {
      const attributesAdditional = getMultipleExternalData(
        dataProductAttributeCombination.entity_product_attribute_combination.entities.product_attribute_combination,
        'id_product_attribute',
        v,
        'id_attribute',
      );
      // Concatenate attributes
      attributes = attributes.concat(attributesAdditional);
      // Remove duplicates
      attributes = attributes.filter((item, pos) => attributes.indexOf(item) === pos);
    });
    const combination = {};
    attributes.forEach((v) => {
      const attributeGroup = getMultipleExternalData(
        dataAttribute.entity_attribute.entities.attribute, 'id', v, 'id_attribute_group',
      ).toString().toLowerCase();
      // Create a new combination group
      if (typeof combination[attributeGroup] === 'undefined') {
        combination[attributeGroup] = [];
      }
      combination[attributeGroup].push(v);
    });

    exportData[reference] = {
      id,
      name,
      nameFR,
      reference,
      category,
      shortDescription,
      description,
      regularPrice,
      price,
      finalPrice,
      discountPercentage,
      discount,
      quantity,
      coverImage,
      thumbnailImage,
      combination,
      weight,
      status,
    };
  });

  const output = `module.exports = { Products: ${beautify(exportData, null, 2, 120)} };`;
  // Write file
  fs.writeFile(outputFile, output, (err) => {
    if (err) {
      console.log(err);
    }
  });
  // Fix lint errors
  exec(`npm run lint:file:fix ${outputFile}`);
}

// Found Occurrences in Directory tests/UI  (12 usages found)
// tests/UI/campaigns/functional/BO/02_orders/01_orders/createOrders  (12 usages found)
//     04_selectPreviousCarts.js  (1 usage found)
//         493 {args: {columnName: 'image', result: Products.demo_1.thumbnailImage}},
//     06_addProductToTheCart.js  (6 usages found)
//         150 thumbnailImage: Products.demo_14.thumbnailImage,
//         395 expect(result.image).to.contains(Products.demo_11.thumbnailImage),
//         412 expect(result.image).to.contains(Products.demo_11.thumbnailImage),
//         446 expect(result.image).to.contains(Products.demo_18.thumbnailImage),
//         504 expect(result.image).to.contains(customizedProduct.thumbnailImage),
//         664 await addOrderPage.waitForVisibleProductImage(page, 3, Products.demo_18.thumbnailImageFR);
//     07_searchAddRemoveVoucher.js  (2 usages found)
//         168 expect(result.image).to.contains(Products.demo_12.thumbnailImage),
//         297 expect(result.image).to.contains(Products.demo_12.thumbnailImage),
//     08_chooseAddress.js  (1 usage found)
//         127 expect(result.image).to.contains(Products.demo_12.thumbnailImage),
//     09_chooseShipping.js  (1 usage found)
//         165 expect(result.image).to.contains(Products.demo_11.thumbnailImage),
//     10_checkSummary.js  (1 usage found)
//         132 expect(result.image).to.contains(Products.demo_12.thumbnailImage),

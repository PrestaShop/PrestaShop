'use strict';
var common = require('../common.webdriverio');
var path = require('path');
var should = require('should');

module.exports = {
  standard: {
    name: "ST",
    new_category_name: "NewStandard",
    features: {
      feature1: {
        custom_value: "5",
      },
    },
    variations: [{
        ref: "variation_1",
        ean13: "1313131313131",
        isbn: "121212121212",
        upc: "012345678901",
        wholesale: "5",
        priceTI: "15",
        weight: "2",
        unity: "10",
        minimal_quantity: "1",
        available_date: common.getCustomDate(30),
        quantity: "20",
      },
      {
        ref: "variation_2",
        ean13: "3131313131313",
        isbn: "212121212121",
        upc: "123456789012",
        wholesale: "10",
        priceTI: "20",
        weight: "-5",
        unity: "-5",
        minimal_quantity: "2",
        available_date: common.getCustomDate(30),
        quantity: "10",
      }]
    },

  pack: {
    name: "PK",
    type: "pack of products",
    new_category_name: "NewPack",
    product_reference: "new product automate",
    pack: {
      pack1: {
        search: "The adventure begins Framed",
        quantity: "1"
      },
      pack2: {
        search: "Today is a good day Framed",
        quantity: "3"
      }
    },
  },

  virtual: {
    name: "VRT",
    type: "virtual",
    new_category_name: "newVirtual",
    product_reference: "new product automate",
    pack: {
      search: "Printed Chiffon Dress",
      quantity: "3"
    },
    attached_file_name: "File to download",
    allowed_number_to_download: "10",
    expiration_date: common.getCustomDate(30),
    number_of_days: '10'
  },

  common: {
    ean13: "0123456789987",
    upc: "012345678901",
    summary: "new automate",
    description: "create new product in automate",
    product_reference: "new product automate",
    wholesale: "5",
    priceTE: "10",
    unitPrice: "10",
    unity: "1",
    isbn: "1234567890123",
    out_stock: "0",
    qty_min: "1",
    qty_date: common.getCustomDate(30),
    qty_msg_stock: "Product avalaible",
    qty_msg_unstock: "Product unavalaible",
    supplier_reference: "ref_supplier",
    quantity: "10",
    product_price: "10",
    cwidth: "10",
    cheight: "10",
    cdepth: "10",
    cweight: "2",
    metatitle: "metatitle",
    metadesc: "metadesc",
    shortlink: "test_auto",
    cadd_ship_coast: "5",
    document_attach: {
      name: "bleue",
      desc: "jpg of bleue"
    },
    personalization: {
      perso_text: {
        name: "perso_text",
      },
      perso_file: {
        name: "perso_file",
      }
    },
    search_related_products: "Hummingbird printed t-shirt (ref:demo_1)//The best is yet to come' Framed poster (ref:demo_6)",
  }
};

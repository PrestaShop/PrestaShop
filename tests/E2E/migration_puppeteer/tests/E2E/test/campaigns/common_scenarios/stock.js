let promise = Promise.resolve();
module.exports = {
  changeStockProductQuantity: function (client, Stock, orderProduct, itemNumber, saveBtn, option = "add") {
    test('should change the product quantity', async () => {
      promise
          .then(async () => await client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity"))
          .then(async () => await page.waitForSelector(Stock.product_quantity_input.replace('%O', orderProduct)));
      if (option === "add") {
        for(let i = 1; i<= itemNumber; i++){
          promise
              .then(async () => await page.waitForSelector(Stock.product_quantity_input.replace('%O', orderProduct),{visible:'true'}))
              .then(async () => await client.waitForExistAndClick(Stock.product_quantity_input.replace('%O', orderProduct),100))
              .then(async () => await page.waitForSelector(Stock.add_quantity_button.replace('%ITEM', orderProduct),{visible:'true'}))
              .then(async () => await page.evaluate((selector) => {document.querySelector(selector).click();},Stock.add_quantity_button.replace('%ITEM', orderProduct)));
        }
      } else {
        for(let i = 1; i<= itemNumber; i++){
          promise
              .then(async () => await page.waitForSelector(Stock.product_quantity_input.replace('%O', orderProduct),{visible:'true'}))
              .then(async () => await client.waitForExistAndClick(Stock.product_quantity_input.replace('%O', orderProduct),100))
              .then(async () => await page.waitForSelector(Stock.remove_quantity_button.replace('%ITEM', orderProduct),{visible:'true'}))
              .then(async () => await page.evaluate((selector) => {document.querySelector(selector).click();},Stock.remove_quantity_button.replace('%ITEM', orderProduct)));
        }
      }
      promise
          .then(async () => await page.waitForSelector(Stock.product_quantity_modified.replace('%O', orderProduct),{visible:'true'}))
          .then(async () => await page.waitForSelector(Stock.available_quantity_modified.replace('%O', orderProduct),{visible:'true'}));

      return promise
          .then(async () => await client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity"))
          .then(async () => await client.checkTextValue(Stock.product_quantity_modified.replace('%O', orderProduct), global.tab["productQuantity"].substring(18), "contain"));
    });
    if (saveBtn === 'checkBtn') {
      test('should click on "Check" button', async () => {
        if (global.tab["productQuantity"] !== 'undefined') {
          global.tab["productQuantity"] = await global.tab["productQuantity"].split(' trending_flat ')[0];
        }
        await client.waitForExistAndClick(Stock.save_product_quantity_button.replace('%I', 1));
      });
      test('should check the success panel', () => {
        return promise
            .then(async () => await client.waitForVisible(Stock.success_hidden_panel))
            .then(async () => {
              await client.checkTextContent(Stock.success_hidden_panel, 'Stock successfully updated');
            });
      });
    }

  },
  changeStockQuantityWithKeyboard: function (client, Stock, orderProduct, itemNumber, saveBtn = '') {
    test('should change quantity to "-5" using the keyboard', async () => {
      await client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity");
      await client.moveToObject(Stock.product_quantity_input.replace('%O', orderProduct));
      for (let i = 1; i <= itemNumber; i++) {
        await client.waitForExistAndClick(Stock.product_quantity_input.replace('%O', orderProduct));
        await client.keys('ArrowDown');
      }
      await client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity");
      await client.checkTextValue(Stock.product_quantity_modified.replace('%O', orderProduct), global.tab["productQuantity"].substring(18), "contain");
    });
    if (saveBtn === 'checkBtn') {
      test('should click on "Check" button', () => client.waitForExistAndClick(Stock.save_product_quantity_button.replace('%I', 1)));
      test('should check the success panel', () => {
        return promise
            .then(() => client.waitForVisible(Stock.success_hidden_panel))
            .then(() => client.checkTextValue(Stock.success_hidden_panel, 'Stock successfully updated', 'contain'));
      });
    }
  },
  checkMovementHistory: function (client, Menu, Movement, movementIndex, itemNumber, option, type, reference = "", dateAndTime = "", productName = "", sort = "false") {
    test('should go to "Movements" tab', async () => {
      return promise
          .then(async () => await client.goToStockMovements(Menu, Movement))
    });
    if (productName !== '' && sort === true) {
      test('should search for the movement', async () => {
        await client.isVisible(Movement.searched_product_close_icon);
        if (global.isVisible) {
          await client.waitForExistAndClick(Movement.searched_product_close_icon,200);
        }
        await client.waitAndSetValue(Movement.search_input, productName);
        await client.waitForExistAndClick(Movement.search_button,200);
        await client.waitForExistAndClick(Movement.advanced_filters_button,200);
        await client.isVisible(Movement.movement_type_select);
        if (global.isVisible) {
          await client.waitForExistAndClick(Movement.movement_type_select);
        }
        await client.waitAndSelectByVisibleText(Movement.movement_type_select, type);
        await client.waitForExistAndClick(Movement.advanced_filters_button);
      });
      test('should sort the movement by date', async () => {
        await client.isVisible(Movement.sort_data_time_icon_desc);
        if (global.isVisible === false) {
          await client.waitForExistAndClick(Movement.sort_data_time_icon,200);
          await page.waitForSelector(Movement.sort_data_time_icon_desc,{visible:'true'});
        }
        else {
          await client.waitForExistAndClick(Movement.sort_data_time_icon,200);
          await page.waitForSelector(Movement.sort_data_time_icon_asc,{visible:'true'});
          await client.waitForExistAndClick(Movement.sort_data_time_icon,200);
          await page.waitForSelector(Movement.sort_data_time_icon_desc,{visible:'true'});

        }
      });
    }

    test('should check movement history', async () => {
      let employee = await "";
      if (global.tab["employee_first_name"] !== undefined && global.tab["employee_last_name"] !== undefined) {
        employee = await global.tab["employee_first_name"] + " " + global.tab["employee_last_name"];
      }
      await client.checkMovement(Movement, movementIndex, itemNumber, option, type, reference, dateAndTime, employee, productName)
    });
  },

  checkStockProduct: function (client, productName, Menu, Stock, availableQuantity, reservedQuantity, physicalQuantity) {
    scenario('Check the stock for the "' + productName + '"', client => {
      test('should go to "Stocks" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu));
      test('should set the "Search products" input', () => client.waitAndSetValue(Stock.search_input, productName, 2000));
      test('should click on "Search" button', () => client.waitForExistAndClick(Stock.search_button, 2000));
      test('should check that the "Available quantity" is equal to ' + availableQuantity + '', () => client.checkTextValue(Stock.available_column.replace("%ID", 1), availableQuantity, 'equal', 2000));
      test('should check the "Reserved quantity" is equal to ' + reservedQuantity + '', () => client.checkTextValue(Stock.employee_column.replace("%O", 1), reservedQuantity));
      test('should check the "Physical quantity" is equal to ' + physicalQuantity + '', () => client.checkTextValue(Stock.physical_column.replace("%ID", 1), physicalQuantity));
    }, 'common_client');
  },
  goToStockPageAndSortByProduct(client, Menu, Stock, searchProduct = false) {
    test('should go to "Stocks" page', async () => {
      await client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu);
      if (searchProduct) {
        await client.isVisible(Stock.search_input, 2000);
        if (global.isVisible) {
          await client.waitAndSetValue(Stock.search_input, "ProductQuantity" + global.date_time, 2000);
          await client.waitForExistAndClick(Stock.search_button, 3000);
        }
      }
      await client.isVisible(Stock.sort_product_icon, 2000);
      if (global.isVisible) {
        await client.waitForVisibleAndClick(Stock.sort_product_icon);
      }

      await client.pause(5000);
    });
  },
  checkAvailableAndPhysicalQuantity(client, quantity, parameter, status, Stock, order) {
    test('should check the "Physical" and "Available" column ' + status, async () => {
      let quantityToCompare = parseInt(global.tab["productQuantity"]) + quantity;
      await client.checkTextValue(Stock.physical_column.replace('%ID', order), String(quantityToCompare), parameter);
      await client.checkTextValue(Stock.available_column.replace('%ID', order), String(quantityToCompare), parameter);
    });
  }
};

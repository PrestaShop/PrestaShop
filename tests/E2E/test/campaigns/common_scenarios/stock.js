let promise = Promise.resolve();
module.exports = {
  changeStockProductQuantity: function (client, Stock, orderProduct, itemNumber, saveBtn, option = "add") {
    test('should change the product quantity', () => {
      promise
        .then(() => client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity"))
        .then(() => client.moveToObject(Stock.product_quantity_input.replace('%O', orderProduct)));
      if (option === "add") {
        for (let i = 1; i <= itemNumber + 1; i++) {
          promise
            .then(() => client.waitForExistAndClick(Stock.add_quantity_button.replace('%ITEM', orderProduct)))
            .then(() => client.pause(1000));
        }
      } else {
        for (let i = 1; i <= itemNumber + 1; i++) {
          promise
            .then(() => client.waitForExistAndClick(Stock.remove_quantity_button.replace('%ITEM', orderProduct)))
            .then(() => client.pause(1000));
        }
      }
      return promise
        .then(() => client.pause(2000))
        .then(() => client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity"))
        .then(() => client.checkTextValue(Stock.product_quantity_modified.replace('%O', orderProduct), global.tab["productQuantity"].substring(18), "contain"));
    });
    if (saveBtn === 'checkBtn') {
      test('should click on "Check" button', () => client.waitForExistAndClick(Stock.save_product_quantity_button));
    }
  },

  checkMovementHistory: function (client, Menu, Movement, movementIndex, itemNumber, option, type, reference = "", dateAndTime = "", productName = "") {
    test('should go to "Movements" tab', () => {
      return promise
        .then(() => client.goToStockMovements(Menu, Movement))
        .then(() => client.pause(5000));
    });
    if (productName !== '') {
      test('should search for the movement', async () => {
        await client.isVisible(Movement.searched_product_close_icon);
        if (global.isVisible) {
          await client.waitForExistAndClick(Movement.searched_product_close_icon);
        }
        await client.waitAndSetValue(Movement.search_input, productName, 2000);
        await client.waitForExistAndClick(Movement.search_button);
        await client.waitForExistAndClick(Movement.advanced_filters_button, 1000);
        await client.waitAndSelectByVisibleText(Movement.movement_type_select, type, 1000);
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
  }
};

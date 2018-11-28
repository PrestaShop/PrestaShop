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

  checkMovementHistory: function (client, Menu, Movement, movementIndex, itemNumber, option, type, reference= "") {
    test('should go to "Movements" tab', () => {
      return promise
        .then(() => client.goToStockMovements(Menu, Movement))
        .then(() => client.pause(5000));
    });
    test('should check movement history', () => client.checkMovement(Movement, movementIndex, itemNumber, option, type, reference));
  }
};

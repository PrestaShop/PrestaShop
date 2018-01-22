module.exports = {
  changeStockProductQuantity : function (client, Stock, orderProduct, itemNumber, option="add"){
    let promise = Promise.resolve();
    test('should change the third product quantity', () => {
      promise
        .then(() => client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity"))
        .then(() => client.moveToObject(Stock.product_quantity_input.replace('%O', orderProduct)));
      if (option == "add"){
        for (let i = 1; i < itemNumber; i++) {
          promise = client.waitForExistAndClick(Stock.add_quantity_button);
        }
      }else{
        for (let i = 1; i < itemNumber; i++) {
          promise = client.waitForExistAndClick(Stock.remove_quantity_button);
        }
      }
      return promise
        .then(() => client.getTextInVar(Stock.product_quantity.replace('%O', orderProduct), "productQuantity"))
        .then(() => client.checkTextValue(Stock.product_quantity_modified.replace('%O', orderProduct), global.tab["productQuantity"].substring(18), "contain"));
    });
    test('should click on "Check" button of the third product quantity', () => client.waitForExistAndClick(Stock.save_product_quantity_button));
  },

  checkMovementHistory : function (client, Movement, movementIndex, itemNumber, option, type) {
    test('should go to "Movements" tab', () => client.goToStockMovements(Movement));
    test('should check movement history', () => client.checkMovement(Movement, movementIndex, itemNumber, option, type));
  }

};
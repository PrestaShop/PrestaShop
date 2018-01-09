const {OrderPage} = require('../../../selectors/BO/order_page');

scenario('Print invoice', client => {
  test('should click on "DOCUMENTS" subtab', () => client.waitForVisibleAndClick(OrderPage.document_submenu));
  test('should download the invoice document', () => client.downloadDocument());
  test('should check the "invoice file name" ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName));
  test('should check that the "invoice customer" is "John Doe"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
  test('should check  the "invoice basic price"  ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.basic_price));
  test('should check that the "invoice product information" is : "Blouse - Size : S- Color : White"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "Blouse - Size : S- Color : White"));
}, 'order/order');

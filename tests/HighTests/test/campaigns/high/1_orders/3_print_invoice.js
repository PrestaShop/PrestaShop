scenario('Print invoice', client => {
  test('should go to "DOCUMENTS"', () => client.goToDocuments());
  test('should download the invoice document', () => client.downloadDocument());
  test('should check invoice name', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName));
  test('should check invoice customer', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
  test('should check invoice basic price', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.basic_price));
  test('should check invoice product information', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "Blouse - Size : S- Color : White"));
}, 'order_BO');

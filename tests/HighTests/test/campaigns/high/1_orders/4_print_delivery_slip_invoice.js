scenario('Print delivery invoice', client => {
  test('should download the delivery invoice document', () => client.downloadDeliveryDocument());
  test('should check delivery invoice name', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName));
  test('should check delivery invoice customer', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
  test('should check delivery invoice product information', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "Blouse - Size : S- Color : White"));
  test('should check delivery invoice product carrier', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "My carrier"));
}, 'order/order', true);


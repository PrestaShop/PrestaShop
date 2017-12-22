scenario('Print delivery invoice', client => {
  test('should download the delivery invoice document', () => client.downloadDeliveryDocument());
  test('should check that the "delivery invoice file name" is :'+global.invoiceFileName , () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName));
  test('should check that the "delivery invoice customer" is : Johan DOE', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
  test('should check that the "delivery invoice product information" is : Blouse - Size : S- Color : White', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "Blouse - Size : S- Color : White"));
  test('should check that the "delivery invoice product carrier" is : My carrier"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "My carrier"));
}, 'order/order', true);


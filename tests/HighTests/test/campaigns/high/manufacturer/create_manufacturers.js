scenario('Create new brand and brand address', () => {

  scenario('Open Browser and Login Back Office', client => {
    test('should open the browser', () => client.open());
    test('should sign in BO', () => client.signinBO());
  }, 'manufacturers');

  scenario('Create new brand', client => {
    test('should go to brands & Suppliers menu', () => client.goToManufacturersList());
    test('should click on add new brand', () => client.addNewBrand());
    test('should add brand name', () => client.addBrandName());
   // test('should add short description', () => client.addShortDescription());
    //test('should add description', () => client.addDescription());
    test('should add brand logo', () => client.addBrandLogo());
    test('should add brand meta title', () => client.addMetaTitle());
    test('should add brand meta description', () => client.addMetaDescription());
    test('should add brand meta keywords', () => client.addMetaKeywords());
    test('should activate the new brand', () => client.activeNewBrand());
    test('should save new brand', () => client.saveBrand());
    test('should check brand success panel', () => client.successPanel('Successful creation.'));
  }, 'manufacturers');

  scenario('Create new brand address', client => {
    test('should go to brands & Suppliers menu', () => client.goToManufacturersList());
    test('should click on new brand address', () => client.addNewBrandAddress());
    test('should Choose the brand', () => client.chooseBrand());
    test('should add brand last name', () => client.addLastName());
    test('should add brand first name', () => client.addFirstName());
    test('should add brand address', () => client.addBrandAddress());
    test('should add brand second address', () => client.addBrandSecondAddress());
    test('should add brand zip code', () => client.addBrandZipCode());
    test('should add brand city', () => client.addBrandCity());
    test('should add brand country', () => client.addBrandCountry());
    test('should add brand phone', () => client.addBrandPhone());
    test('should add brand information', () => client.addBrandOtherInformation());
    test('should save brand address', () => client.saveBrandAddress());
    test('should check brand success panel', () => client.successPanel('Successful creation.'));
  }, 'manufacturers');

  scenario('Sign Out Back Office', client => {
    test('should sign out BO', () => client.signoutBO());
  }, 'manufacturers')

}, 'manufacturers', true);

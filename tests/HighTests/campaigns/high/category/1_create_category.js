scenario('Create category', client => {
    test('open browser', () => client.open());
    test('sign in', () => client.loginBO());
    test('go to category', () => client.goToCategoryList());
    test('create category', () => client.createCategory());
    test('add category name', () => client.addCategoryName());
    test('add category image', () => client.addCategoryImage());
    test('add category thumb', () => client.addCategoryThumb());
    test('add category title', () => client.addCategoryTitle());
    test('add category meta desciption', () => client.addCategoryMetaDescription());
    test('add category meta keys words', () => client.addCategoryMetakeyswords());
    test('add category simplify url', () => client.addCategorySimplifyUrl());
    test('add category save', () => client.addCategorySave());
    test('add category success panel', () => client.addCategorySuccessPanel('Création réussie.','the category is not created !'));
},'category',true);

scenario('Check category in BO', client => {
    test('open browser', () => client.open());
    test('sign in', () => client.loginBO());
    test('go to category ', () => client.goToCategoryBO());
    test('search category ', () => client.searchCategoryBO());
    test('check category image', () => client.checkCategoryImage());
    test('check category image thumb', () => client.checkCategoryImageThumb());
    test('check category title', () => client.checkCategoryTitle());
    test('check category meta description', () => client.checkCategoryMetaDescription());
 // test('check category keywords', () => client.checkCategorykeyswordsText());
    test('check category simplify url', () => client.checkCategorySimplifyURL());
},'category',true);


scenario('Check category in FO', client => {
    test('open browser', () => client.open());
    test('sign in FO', () => client.loginFO());
    test('open product list', () => client.openProductList());
    test('check category existence', () => client.checkcategoryexistenceFO());
},'category',true);

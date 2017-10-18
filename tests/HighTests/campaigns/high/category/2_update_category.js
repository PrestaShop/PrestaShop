scenario('Update category', client => {
    test('open browser', () => client.open());
    test('sign in', () => client.loginBO());
    test('go to category', () => client.goToCategoryList());
    test('search for the category', () => client.searchCategoryBO());
    test('update category', () => client.updateCategory());
    test('update category success panel', () => client.addCategorySuccessPanel('Mise à jour réussie.','the category is not updated!'));
},'category',true);

scenario('Check updated category in BO', client => {
    test('open browser', () => client.open());
    test('sign in', () => client.loginBO());
    test('go to category ', () => client.goToCategoryBO());
    test('search category ', () => client.searchCategoryBO());
    test('check category image', () => client.checkCategoryImage());
    test('check category image thumb', () => client.checkCategoryImageThumb());
    test('check category title', () => client.checkCategoryTitle());
    test('check category meta description', () => client.checkCategoryMetaDescription());
    //   test('check category keywords', () => client.checkCategorykeyswordsText());
    test('check category simplify url', () => client.checkCategorySimplifyURL());
},'category',true);

scenario('Check updated category in FO', client => {
    test('open browser', () => client.open());
    test('sign in FO', () => client.loginFO());
    test('open product list', () => client.openProductList());
    test('check category length', () => client.checkcategoryexistenceFO());
},'category',true);

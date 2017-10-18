scenario('Delete category', client => {
    test('open browser', () => client.open());
    test('sign in', () => client.loginBO());
    test('go to category', () => client.goToCategoryList());
    test('search for the category', () => client.searchCategoryBO());
    test('update category', () => client.deleteCategory());
    test('update category success panel', () => client.addCategorySuccessPanel('Suppression réussie.','the category is not deleted !'));
},'category',true);

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

scenario('Delete category with action group', client => {
    test('open browser', () => client.open());
    test('sign in', () => client.loginBO());
    test('go to category', () => client.goToCategoryList());
    test('search for the category', () => client.searchCategoryBO());
    test('update category', () => client.deleteCategoryWithActiongroup());
    test('update category success panel', () => client.addCategorySuccessPanel('Sélection supprimée avec succès','the category is not deleted !'));
},'category',true);

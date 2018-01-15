const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {productPage}= require('../../../selectors/FO/product_page');
let promise = Promise.resolve();

scenario('Check that all products are well displayed', client => {
    test('should open the browser', () => client.open());
    test('should access to the Front Office', () => client.accessToFO(AccessPageFO));
    test('should click on "SEE ALL PRODUCTS" link', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
    test('should set the language of shop to "English"', () => client.changeLanguage());
    test('should check the existence of pagination', () => {
        return promise
            .then(() => client.isVisible(productPage.pagination_next))
            .then(() => client.clickPageNext(productPage.pagination_next))
    });
    test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product_all));
}, 'product/product');
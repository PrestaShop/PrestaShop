/* global browser */

import fixtures from '../fixtures';

export function addSomeProductToCart () {
    return browser
        .url(fixtures.urls.aCategoryWithProducts)
        .click('.product-miniature:nth-of-type(2)')
        .click('[name=add]')
    ;
};

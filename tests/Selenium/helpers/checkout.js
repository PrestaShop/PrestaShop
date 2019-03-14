/* global browser */

import fixtures from '../fixtures';

export function addSomeProductToCart () {
    return browser
        .url(fixtures.urls.aCategoryWithProducts)
        .click('.product-miniature:nth-of-type(2) a.product-thumbnail')
        .click('[data-button-action="add-to-cart"]')
        .waitForVisible('#blockcart-modal')
    ;
};

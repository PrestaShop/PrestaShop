/* global browser */

import fixtures from '../fixtures';

export function addSomeProductToCart () {
    return browser
        .url(fixtures.urls.aCategoryWithProducts)
        .click('[data-link-action="add-to-cart"]')
        .waitForVisible('#blockcart-modal')
    ;
};

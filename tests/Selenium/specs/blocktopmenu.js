/* global describe, it, browser */

describe('The blocktopmenu', function () {
  it('should be shown on the home page', function () {
    return browser
        .url('/')
        .then(function () {
            return browser.element('div.menu');
        })
    ;
  });
});

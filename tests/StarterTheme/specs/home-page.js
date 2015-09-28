/* global describe, it, browser */

describe('The home page', function () {
  it('should contain a link with the logo', function () {
    return browser
      .url('/')
      .then(function () {
        return browser.element('a.logo img');
      })
    ;
  });
});

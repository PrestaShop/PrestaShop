/* global describe, it, browser */

describe('The home page', function () {
  it('should contain a link with the logo', function () {
    return browser
      .url('/')
      .element('a.logo img')
    ;
  });
});

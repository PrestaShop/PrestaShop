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
  it('should open when hovered', function () {
    return browser
      .url('/')
      .moveToObject('div.menu')
      .then(function () {
        return browser.element('.menu-images-container');
      })
    ;
  });
});

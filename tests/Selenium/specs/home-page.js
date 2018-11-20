/* global describe, it, browser */

import {baseUrl} from '../settings';

describe('The home page', function () {
  it('should contain a link with the logo', function () {
    return browser
      .url('/')
      .isVisible('a[href^="' + baseUrl + '"] img')
    ;
  });
});

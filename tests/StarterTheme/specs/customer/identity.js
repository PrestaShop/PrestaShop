/* global describe, it, browser, before, after */

import fixtures from '../../fixtures';

describe('The Customer Identity Page', function () {
  before(() => {
    return browser
      .loginDefaultCustomer()
      .url(fixtures.urls.identity)
    ;
  });

  it('should show the customer form', function () {
    return browser.isVisible('#customer-form').should.become(true);
  });

  it('should refuse to save the customer if the wrong password is provided', function () {
    return browser
      .setValue('[name="password"]', 'wrong password')
      .click('#customer-form button')
      .waitForVisible('.notification-error')
    ;
  });

  it('should save the customer if the correct password is provided', function () {
    return browser
      .setValue('[name="password"]', '123456789')
      .click('#customer-form button')
      .waitForVisible('.notification-success')
    ;
  });

  it('should allow the customer to change their password', function () {
    return browser
      // change the password
      .setValue('[name="password"]', '123456789')
      .setValue('[name="new_password"]', 'new password')
      .click('#customer-form button')
      .waitForVisible('.notification-success')
      // try to login with the new password
      .logout()
      .loginDefaultCustomer({password: 'new password'})
      // change it back to initial password
      .url(fixtures.urls.identity)
      .setValue('[name="password"]', 'new password')
      .setValue('[name="new_password"]', '123456789')
      .click('#customer-form button')
      .waitForVisible('.notification-success')
    ;
  });

  after(() => {
    return browser.logout();
  });
});

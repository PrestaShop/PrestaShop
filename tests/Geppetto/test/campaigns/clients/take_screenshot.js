const authentication = require('./common_scenarios/authentication');

scenario('Test mocha with puppeteer', () => {
  authentication.signInBO('take_screenshot');
  scenario('Take a screenshot', client => {
    test('should take successfuly a screenshot', () => client.screenshot());
  }, 'common_client');
  authentication.signOutBO();
}, 'common_client', true);

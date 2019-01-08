const Install = require('../../../../campaigns/clients/common_scenarios/install/install');

scenario('Install the shop', client => {
  scenario('Open the browser then access to install page', client => {
    test('should open the browser', async () => {
      await client.open();
      await client.startTracing('installShop');
    });
    test('should go to the install page', async () => {
      await client.openShopURL(global.installFolderName);
      await client.waitFor(5000);

    });
    Install.installShop(global.language, ['en']);
    scenario('Sign in the "Front Office"', client => {
      test('should sign in the Front Office', async () => {
        await client.openShopURL();
        await client.signInFO();
      });
    }, 'common_client');
  }, 'common_client');

}, 'common_client', true);








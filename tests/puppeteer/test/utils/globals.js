global.URL_FO = process.env.URL_FO || 'http://localhost/prestashop/';
global.URL_BO = process.env.URL_BO || URL_FO + 'admin-dev/';
global.EMAIL = process.env.LOGIN || 'demo@prestashop.com';
global.PASSWD = process.env.PASSWD || 'prestashop_demo';
global.HEADLESS = process.env.HEADLESS || true;
global.browser = null;

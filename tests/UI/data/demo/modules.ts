import ModuleData from '@data/faker/module';

export default {
  blockwishlist: new ModuleData({
    tag: 'blockwishlist',
    name: 'Wishlist',
  }),
  psCashOnDelivery: new ModuleData({
    tag: 'ps_cashondelivery',
    name: 'Cash on delivery (COD)',
  }),
  psCheckPayment: new ModuleData({
    tag: 'ps_checkpayment',
    name: 'Payments by check',
  }),
  psEmailSubscription: new ModuleData({
    tag: 'ps_emailsubscription',
    name: 'Newsletter subscription',
  }),
  psFacetedSearch: new ModuleData({
    tag: 'ps_facetedsearch',
    name: 'Faceted search',
    releaseZip: 'https://github.com/PrestaShop/ps_facetedsearch/releases/download/v3.12.1/ps_facetedsearch.zip',
  }),
  contactForm: new ModuleData({
    tag: 'contactform',
    name: 'Contact form',
  }),
  themeCustomization: new ModuleData({
    tag: 'ps_themecusto',
    name: 'Theme Customization',
  }),
  availableQuantities: new ModuleData({
    tag: 'statsstock',
    name: 'Available quantities',
  }),
  mainMenu: new ModuleData({
    tag: 'ps_mainmenu',
    name: 'Main menu',
  }),
  keycloak: new ModuleData({
    tag: 'keycloak_connector_demo',
    name: 'Keycloak OAuth2 connector demo',
    releaseZip: 'https://github.com/PrestaShop/keycloak_connector_demo/releases/download/v1.0.4/keycloak_connector_demo.zip',
  }),
};

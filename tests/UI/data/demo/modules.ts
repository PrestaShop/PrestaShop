import ModuleData from '@data/faker/module';

export default {
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
  }),
};

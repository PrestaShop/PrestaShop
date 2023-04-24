import ModuleData from '@data/faker/module';

export default {
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
};

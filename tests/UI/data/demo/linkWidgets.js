import Hooks from '@data/demo/hooks';

module.exports = {
  LinkWidgets: {
    demo_1: {
      name: 'Footer test block',
      frName: 'Footer test block',
      hook: Hooks.displayFooter.name,
      contentPages: ['Delivery'],
      productsPages: ['New products'],
      staticPages: ['Contact us'],
      customPages: [{name: 'Home in footer', url: global.FO.URL}],
    },
  },
};

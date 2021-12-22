const {hooks} = require('@data/demo/hooks');

module.exports = {
  LinkWidgets: {
    demo_1: {
      name: 'Footer test block',
      frName: 'Footer test block',
      hook: hooks.displayFooter.name,
      contentPages: ['Delivery'],
      productsPages: ['New products'],
      staticPages: ['Contact us'],
      customPages: [{name: 'Home in footer', url: global.FO.URL}],
    },
  },
};

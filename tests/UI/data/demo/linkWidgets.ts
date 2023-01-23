import Hooks from '@data/demo/hooks';
import LinkWidgetData from '@data/faker/linkWidget';

export default {
  demo_1: new LinkWidgetData({
    name: 'Footer test block',
    frName: 'Footer test block',
    hook: Hooks.displayFooter,
    contentPages: ['Delivery'],
    productsPages: ['New products'],
    staticPages: ['Contact us'],
    customPages: [
      {
        name: 'Home in footer',
        url: global.FO.URL,
      },
    ],
  }),
};

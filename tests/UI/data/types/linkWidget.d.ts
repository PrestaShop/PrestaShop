import HookData from '@data/faker/hook';

type LinkWidgetCreator = {
  name?: string
  frName?: string
  hook?: HookData
  contentPages?: string[]
  productsPages?: string[]
  staticPages?: string[]
  customPages?: LinkWidgetPage[]
};

type LinkWidgetPage = {
  name: string
  url: string
};

export {
  LinkWidgetCreator,
  LinkWidgetPage,
};

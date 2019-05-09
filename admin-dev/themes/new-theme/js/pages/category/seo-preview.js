import Serp from '../../app/utils/serp/index';

const $ = window.$;


const FormSeoPreview = (selectors) => {
  new Serp(
    {
      container: '#serp-app',
      defaultTitle: selectors.title,
      watchedTitle: selectors.metaTitle,
      defaultDescription: selectors.description,
      watchedDescription: selectors.metaDescription,
      watchedMetaUrl: selectors.metaUrl,
      multiLanguageInput: selectors.multiLanguageInput,
      multiLanguageItem: selectors.multiLanguageItem,
    },
    $('#serp-app').data('category-url'),
  );
};

export default FormSeoPreview;

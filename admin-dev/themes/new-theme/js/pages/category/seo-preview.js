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
    },
    'http://google.com/art.html',
    true
  );
};

export default FormSeoPreview;

import Router from '@components/router';

const {$} = window;

export function getFeatureValues(idFeature) {
  const router = new Router();

  return $.get(router.generate('admin_feature_get_feature_values', {idFeature}));
}

export default {
  getFeatureValues,
};

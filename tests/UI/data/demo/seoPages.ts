import SeoPageData from '@data/faker/seoPage';

export default {
  pageNotFound: new SeoPageData({
    id: 1,
    page: 'pagenotfound',
    title: '404 error',
    friendlyUrl: 'page-not-found',
  }),
  best_sales: new SeoPageData({
    id: 2,
    page: 'best-sales',
    title: 'Best sales',
    friendlyUrl: 'best-sales',
  }),
  contact: new SeoPageData({
    id: 3,
    page: 'contact',
    title: 'Contact us',
    friendlyUrl: 'contact-us',
  }),
  orderReturn: new SeoPageData({
    page: 'order-return',
    title: 'Order return',
    friendlyUrl: 'order-return',
  }),
  pdfOrderReturn: new SeoPageData({
    page: 'pdf-order-return',
    title: 'Pdf order return',
    friendlyUrl: 'pdf-order-return',
  }),
};

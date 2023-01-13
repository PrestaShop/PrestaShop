import CMSPageData from '@data/faker/CMSpage';

export default {
  delivery: new CMSPageData({
    id: 1,
    url: 'delivery',
    title: 'Delivery',
    metaTitle: '',
    position: 1,
    displayed: true,
  }),
  legalNotice: new CMSPageData({
    id: 2,
    url: 'legal-notice',
    title: 'Legal Notice',
    metaTitle: '',
    position: 2,
    displayed: true,
  }),
  termsAndCondition: new CMSPageData({
    id: 3,
    url: 'terms-and-conditions-of-use',
    title: 'Terms and conditions of use',
    metaTitle: '',
    position: 3,
    displayed: true,
  }),
  aboutUs: new CMSPageData({
    id: 4,
    url: 'about-us',
    title: 'About us',
    metaTitle: '',
    position: 4,
    displayed: true,
  }),
  securePayment: new CMSPageData({
    id: 5,
    url: 'secure-payment',
    title: 'Secure payment',
    metaTitle: '',
    position: 5,
    displayed: true,
  }),
};

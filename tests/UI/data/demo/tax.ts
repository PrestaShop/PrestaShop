import TaxData from '@data/faker/tax';

export default {
  DefaultFrTax: new TaxData({
    id: 1,
    name: 'TVA FR 20%',
    rate: '20',
    enabled: true,
  }),
  VatUkTax: new TaxData({
    id: 15,
    name: 'VAT UK 20%',
    rate: '20',
    enabled: true,
  }),
};

import TaxRuleData from '@data/faker/taxRule';

export default [
  new TaxRuleData({
    id: 1,
    name: 'FR Taux standard (20%)',
  }),
  new TaxRuleData({
    id: 2,
    name: 'FR Taux réduit (10%)',
  }),
  new TaxRuleData({
    id: 3,
    name: 'FR Taux réduit (5.5%)',
  }),
  new TaxRuleData({
    id: 4,
    name: 'FR Taux super réduit (2.1%)',
  }),
  new TaxRuleData({
    id: 5,
    name: 'EU VAT For Virtual Products',
  }),
];

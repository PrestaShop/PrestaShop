import {
  FakerTaxRule,
} from '@prestashop-core/ui-testing';

export default [
  new FakerTaxRule({
    id: 1,
    name: 'FR Taux standard (20%)',
  }),
  new FakerTaxRule({
    id: 2,
    name: 'FR Taux réduit (10%)',
  }),
  new FakerTaxRule({
    id: 3,
    name: 'FR Taux réduit (5.5%)',
  }),
  new FakerTaxRule({
    id: 4,
    name: 'FR Taux super réduit (2.1%)',
  }),
  new FakerTaxRule({
    id: 5,
    name: 'EU VAT For Virtual Products',
  }),
];

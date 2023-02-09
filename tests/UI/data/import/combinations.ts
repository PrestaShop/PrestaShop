import ImportData from '@data/faker/import';
import type {ImportCombination} from '@data/types/import';

const records: ImportCombination[] = [
  {
    id: 20,
    reference: 'reference_1',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 21,
    reference: 'reference_2',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 22,
    reference: 'reference_3',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 23,
    reference: 'reference_4',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 24,
    reference: 'reference_5',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 25,
    reference: 'reference_6',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 26,
    reference: 'reference_7',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 27,
    reference: 'reference_8',
    attribute: 'Color:color:0',
    value: 'Home',
  },
  {
    id: 28,
    reference: 'reference_9',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 29,
    reference: 'reference_10',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
  {
    id: 30,
    reference: 'reference_11',
    attribute: 'Color:color:0',
    value: 'Blue:0',
  },
];

export default new ImportData({
  entity: 'Combinations',
  header: [
    {id: 'id', title: 'Product ID*'},
    {id: 'reference', title: 'Product_Reference'},
    {id: 'attribute', title: 'Attribute (Name:Type:Position)*'},
    {id: 'value', title: 'Value (Value:Position)*'},
  ],
  records,
});

import AttributeData from '@data/faker/attribute';
import AttributeValueData from '@data/faker/attributeValue';

export default {
  size: new AttributeData({
    id: 1,
    name: 'Size',
    values: [
      // 0 : small
      new AttributeValueData({
        id: 1,
        value: 'S',
        position: 1,
      }),
      // 1 : medium
      new AttributeValueData({
        id: 1,
        value: 'M',
        position: 1,
      }),
      // 2 : large
      new AttributeValueData({
        id: 1,
        value: 'L',
        position: 1,
      }),
      // 3 : xLarge
      new AttributeValueData({
        id: 1,
        value: 'XL',
        position: 1,
      }),
    ],
    position: 1,
  }),
  color: new AttributeData({
    id: 2,
    name: 'Color',
    values: [
      // 0 : grey
      new AttributeValueData({
        id: 5, value: 'Grey', color: '#AAB2BD', position: 1,
      }),
      // 1 : taupe
      new AttributeValueData({
        id: 6, value: 'Taupe', color: '#CFC4A6', position: 2,
      }),
      // 2 : beige
      new AttributeValueData({
        id: 7, value: 'Beige', color: '#f5f5dc', position: 3,
      }),
      // 3 : white
      new AttributeValueData({
        id: 8, value: 'White', color: '#ffffff', position: 4,
      }),
      // 4 : offWhite
      new AttributeValueData({
        id: 9, value: 'Off White', color: '#faebd7', position: 5,
      }),
      // 5 : red
      new AttributeValueData({
        id: 10, value: 'Red', color: '#E84C3D', position: 6,
      }),
      // 6 : black
      new AttributeValueData({
        id: 11, value: 'Black', color: '#434A54', position: 7,
      }),
      // 7 : camel
      new AttributeValueData({
        id: 12, value: 'Camel', color: '#C19A6B', position: 8,
      }),
      // 8 : orange
      new AttributeValueData({
        id: 13, value: 'Orange', color: '#F39C11', position: 9,
      }),
      // 9 : blue
      new AttributeValueData({
        id: 14, value: 'Blue', color: '#5D9CEC', position: 10,
      }),
      // 10 : green
      new AttributeValueData({
        id: 15, value: 'Green', color: '#A0D468', position: 11,
      }),
      // 11 : yellow
      new AttributeValueData({
        id: 16, value: 'Yellow', color: '#F1C40F', position: 12,
      }),
      // 12 : brown
      new AttributeValueData({
        id: 17, value: 'Brown', color: '#964B00', position: 13,
      }),
      // 13 : pink
      new AttributeValueData({
        id: 18, value: 'Pink', color: '#FCCACD', position: 14,
      }),
    ],
    position: 2,
  }),
  dimension: new AttributeData({
    id: 3,
    name: 'Dimension',
    values: [
      // 0 : first
      new AttributeValueData({id: 19, value: '40*60cm', position: 1}),
      // 1 : second
      new AttributeValueData({id: 20, value: '60*90cm', position: 2}),
      // 2 : third
      new AttributeValueData({id: 21, value: '80*120cm', position: 3}),
    ],
    position: 3,
  }),
  paperType: new AttributeData({
    id: 4,
    name: 'Paper Type',
    values: [
      // 0 : ruled
      new AttributeValueData({id: 22, value: 'Rules', position: 1}),
      // 1 : plain
      new AttributeValueData({id: 23, value: 'Plain', position: 2}),
      // 2 : squared
      new AttributeValueData({id: 24, value: 'Squared', position: 3}),
      // 3 : doted
      new AttributeValueData({id: 25, value: 'Doted', position: 4}),
    ],
    position: 4,
    displayed: true,
  }),
};

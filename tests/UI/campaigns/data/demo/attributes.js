module.exports = {
  Attributes: {
    size: {
      id: 1,
      name: 'Size',
      values: {
        small: 'S',
        medium: 'M',
        large: 'L',
        xLarge: 'XL',
      },
    },
    color: {
      id: 2,
      name: 'Color',
      values: {
        grey: 'Grey',
        taupe: 'Taupe',
        beige: 'Beige',
        white: 'White',
        offWhite: 'Off White',
        red: 'Red',
        black: 'Black',
        camel: 'Camel',
        orange: 'Orange',
        blue: 'Blue',
        green: 'Green',
        yellow: 'Yellow',
        brown: 'Brown',
        pink: 'Pink',
      },
    },
    dimension: {
      id: 3,
      name: 'Dimension',
      values: {
        first: '40*60cm',
        second: '60*90cm',
        third: '80*120cm',
      },
    },
    paperType: {
      id: 4,
      name: 'Paper Type',
      values: {
        ruled: 'Rules',
        plain: 'Plain',
        squarred: 'Squarred',
        doted: 'Doted',
      },
      displayed: true,
    },
  },
};

module.exports = {
  Attributes: {
    size: {
      id: 1,
      name: 'Size',
      values: {
        small: {id: 1, value: 'S', position: 1},
        medium: {id: 1, value: 'M', position: 1},
        large: {id: 1, value: 'L', position: 1},
        xLarge: {id: 1, value: 'XL', position: 1},
      },
      position: 1,
    },
    color: {
      id: 2,
      name: 'Color',
      values: {
        grey: {id: 5, value: 'Grey', position: 1},
        taupe: {id: 6, value: 'Taupe', position: 2},
        beige: {id: 7, value: 'Beige', position: 3},
        white: {id: 8, value: 'White', position: 4},
        offWhite: {id: 9, value: 'Off White', position: 5},
        red: {id: 10, value: 'Red', position: 6},
        black: {id: 11, value: 'Black', position: 7},
        camel: {id: 12, value: 'Camel', position: 8},
        orange: {id: 13, value: 'Orange', position: 9},
        blue: {id: 14, value: 'Blue', position: 10},
        green: {id: 15, value: 'Green', position: 11},
        yellow: {id: 16, value: 'Yellow', position: 12},
        brown: {id: 17, value: 'Brown', position: 13},
        pink: {id: 18, value: 'Pink', position: 14},
      },
      position: 2,
    },
    dimension: {
      id: 3,
      name: 'Dimension',
      values: {
        first: {id: 19, value: '40*60cm', position: 1},
        second: {id: 20, value: '60*90cm', position: 2},
        third: {id: 21, value: '80*120cm', position: 3},
      },
      position: 3,
    },
    paperType: {
      id: 4,
      name: 'Paper Type',
      values: {
        ruled: {id: 22, value: 'Rules', position: 1},
        plain: {id: 23, value: 'Plain', position: 2},
        squarred: {id: 24, value: 'Squarred', position: 3},
        doted: {id: 25, value: 'Doted', position: 4},
      },
      position: 4,
      displayed: true,
    },
  },
};

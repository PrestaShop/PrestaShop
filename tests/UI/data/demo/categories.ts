import CategoryData from '@data/faker/category';

const category4: CategoryData = new CategoryData({
  id: 4,
  name: 'Men',
  description: 'T-shirts, sweaters, hoodies and men\'s accessories.',
  position: 1,
  displayed: true,
});
const category5: CategoryData = new CategoryData({
  id: 5,
  name: 'Women',
  description: 'T-shirts, sweaters, hoodies and women\'s accessories. From basics to original creations, '
    + 'for every style.',
  position: 2,
  displayed: true,
});
const category7: CategoryData = new CategoryData({
  id: 7,
  name: 'Stationery',
  description: 'Notebooks, agendas, office accessories and more.',
  position: 1,
  displayed: true,
});
const category8: CategoryData = new CategoryData({
  id: 8,
  name: 'Home Accessories',
  description: 'Details matter! Liven up your interior with our selection of home accessories.',
  position: 2,
  displayed: true,
});

export default {
  home: new CategoryData({
    id: 2,
    name: 'Home',
    description: '',
    metaTitle: '',
    metaDescription: '',
    displayed: true,
  }),
  clothes: new CategoryData({
    id: 3,
    name: 'Clothes',
    description: 'Discover our favorites fashionable discoveries, a selection of cool items to integrate in your '
      + 'wardrobe. Compose a unique style with personality which matches your own.',
    position: 1,
    displayed: true,
    children: [
      category4,
      category5,
    ],
  }),
  men: category4,
  women: category5,
  accessories: new CategoryData({
    id: 6,
    name: 'Accessories',
    description: 'Items and accessories for your desk',
    position: 2,
    displayed: true,
    children: [
      category7,
      category8,
    ],
  }),
  stationery: category7,
  homeAccessories: category8,
  art: new CategoryData({
    id: 9,
    name: 'Art',
    description: 'Framed poster and vector images',
    position: 3,
    displayed: true,
  }),
};

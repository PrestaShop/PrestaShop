module.exports = {
  Carriers: [
    {
      id: 1,
      name: 'prestashop',
      delay: 'Pick up in-store',
      status: true,
      freeShipping: true,
      position: 1,
    },
    {
      id: 2,
      name: 'My carrier',
      delay: 'Pick up in-store',
      status: true,
      freeShipping: false,
      position: 2,
    },
    {
      id: 3,
      name: 'My cheap carrier',
      delay: 'Buy more to pay less!',
      status: false,
      freeShipping: false,
      position: 3,
    },
    {
      id: 4,
      name: 'My light carrier',
      delay: 'The lighter the cheaper!',
      status: false,
      freeShipping: false,
      position: 4,
    },
  ],
};

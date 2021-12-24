module.exports = {
  Carriers: {
    default: {
      id: 1,
      name: global.INSTALL.SHOP_NAME,
      delay: 'Pick up in-store',
      status: true,
      freeShipping: true,
      position: 1,
    },
    myCarrier: {
      id: 2,
      name: 'My carrier',
      delay: 'Delivery next day!',
      status: true,
      freeShipping: false,
      position: 2,
    },
    cheapCarrier: {
      id: 3,
      name: 'My cheap carrier',
      delay: 'Buy more to pay less!',
      status: false,
      freeShipping: false,
      position: 3,
    },
    lightCarrier: {
      id: 4,
      name: 'My light carrier',
      delay: 'The lighter the cheaper!',
      status: false,
      freeShipping: false,
      position: 4,
    },
  },
};

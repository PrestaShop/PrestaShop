module.exports = {
    aProductWithVariants: {
        id: 5,
        // Format is: key = id of attribute group, value = id of attribute value
        defaultVariant: {
            '1': 1,
            '3': 16
        },
        anotherVariant: {
            '1': 3,
            '3': 14
        }
    },
    aCustomizableProduct: {
      id: 1
    },
    urls: {
      login: '/en/login',
      myAccount: '/en/my-account',
      myAddresses: '/en/addresses',
      address: '/en/address',
      checkout: '/en/order',
      orderhistory: '/en/order-history',
      orderdetail: '/en/index.php?controller=order-detail&id_order=5',
      aCategoryWithProducts: '/en/3-women',
      identity: '/en/identity',
      adminLogin: '/admin-dev'
    },
    customer: {
      email: 'pub@prestashop.com',
      password: '123456789'
    }
};

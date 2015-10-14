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
      address: 'en/address',
      opc: '/en/quick-order',
      logout: '/en/logout'
    },
    customer: {
      email: 'pub@prestashop.com',
      password: '123456789'
    }
};

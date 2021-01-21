module.exports = {
  behaviour: ['This tax only', 'Combine', 'One after another'],

  taxRules: [
    {
      id: 1,
      name: 'FR Taux standard (20%)',
      enabled: true,
    },
    {
      id: 2,
      name: 'FR Taux réduit (10%)',
      enabled: true,
    },
    {
      id: 3,
      name: 'FR Taux réduit (5.5%)',
      enabled: true,
    },
    {
      id: 4,
      name: 'FR Taux super réduit (2.1%)',
      enabled: true,
    },
    {
      id: 5,
      name: 'EU VAT For Virtual Products',
      enabled: true,
    },
  ],
};

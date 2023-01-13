import EmployeeData from '@data/faker/employee';

export default {
  DefaultEmployee: new EmployeeData({
    id: 1,
    firstName: 'Marc',
    lastName: 'Beier',
    email: 'demo@prestashop.com',
    password: 'Correct Horse Battery Staple',
    defaultPage: 'Dashboard',
    language: 'English (English)',
    active: true,
  }),
};

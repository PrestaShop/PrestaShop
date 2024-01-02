import EmployeeData from '@data/faker/employee';

export default {
  DefaultEmployee: new EmployeeData({
    id: 1,
    firstName: global.BO.FIRSTNAME,
    lastName: global.BO.LASTNAME,
    email: global.BO.EMAIL,
    password: global.BO.PASSWD,
    defaultPage: 'Dashboard',
    language: 'English (English)',
    active: true,
  }),
};

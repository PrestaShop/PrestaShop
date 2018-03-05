const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('../../../high/09_customer/customer');

let customerData = {
  first_name: 'demo',
  last_name: 'demo',
  email_address: 'demo',
  password: '123456789',
  birthday: {
    day: '18',
    month: '12',
    year: '1991'
  }
};

let editCustomerData = {
  first_name: 'CustomerFirstName',
  last_name: 'CustomerFirstName',
  email_address: 'CustomerFirstName',
  password: '123456789',
  birthday: {
    day: '31',
    month: '3',
    year: '1994'
  }
};

require('../../../high/09_customer/1_create_customer');



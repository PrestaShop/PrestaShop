var faker = require('faker');

export function getRandomUser () {
  const defaultUser = {
    name: {
      first: faker.name.firstName(),
      last: faker.name.lastName(),
    },
    email: faker.internet.email(),
    location: {
      street: faker.address.streetAddress(),
      city: faker.address.city()
    }
  };

  return new Promise((resolve, reject) => {
    resolve(defaultUser);
  });
}

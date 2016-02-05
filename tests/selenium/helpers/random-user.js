import request from 'request';

export function getRandomUser () {
  const defaultUser = {
    name: {
      first: "Example",
      last: "User",
    },
    email: `user${Date.now()}@example.com`,
    location: {
      street: "777, Main Street",
      city: "FileSystem"
    }
  };

  return new Promise((resolve, reject) => {

    setTimeout(() => resolve(defaultUser), 2000);

    request.get({
      url: 'https://randomuser.me/api/',
      json: true
    }, (error, response, body) => {
      if (error) {
          resolve(defaultUser);
      } else if (body.results) {
        const user = body.results[0].user;
        // sometimes we get weird e-mails from the API
        user.email = user.email.replace(/[^a-z@.]/g, '_');
        resolve(user);
      } else {
        resolve(defaultUser);
      }
    });
  });
}

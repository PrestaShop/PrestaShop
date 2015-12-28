import request from 'request';

export function getRandomUser () {
  return new Promise((resolve, reject) => {
    request.get({
      url: 'https://randomuser.me/api/',
      json: true
    }, (error, response, body) => {
      if (error) {
          reject(error);
      } else {
        const user = body.results[0].user;
        // sometimes we get weird e-mails from the API
        user.email = user.email.replace(/[^a-z@.]/g, '_');
        resolve(user);
      }
    });
  });
}

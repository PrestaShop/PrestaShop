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
                resolve(body.results[0].user);
            }
        });
    });

}

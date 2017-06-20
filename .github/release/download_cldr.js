const http = require('http');
const fs = require('fs');
const path = require('path');

const rootPath = path.normalize(`${__dirname}/../..`);

var file = fs.createWriteStream(`${rootPath}/translations/cldr.zip`);
http.get("http://i18n.prestashop.com/cldr/cldr.zip", function(response) {
  response.pipe(file);
});

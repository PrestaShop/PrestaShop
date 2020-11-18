const fs = require('fs');
const path = require('path');

var argv = require('yargs')
    .usage('Usage: $0 --version [num]')
    .demandOption(['version'])
    .describe('version', 'Version of the release')
    .example('$0 --version 1.7.1.0', 'Update version number everywhere required')
    .argv;

const rootPath = path.normalize(`${__dirname}/../..`);


// Update constants
fs.readFile(`${rootPath}/config/defines.inc.php`, 'utf8', (err, content) => {
  if (err) throw err;
  fs.writeFileSync(
    `${rootPath}/config/defines.inc.php`,
    content
      .replace(/define(.*)_PS_MODE_DEV_(.*);/i, 'define(\'_PS_MODE_DEV_\', false);')
      .replace(/define(.*)_PS_DISPLAY_COMPATIBILITY_WARNING_(.*);/i, 'define(\'_PS_DISPLAY_COMPATIBILITY_WARNING_\', false);')
  );
});


// Update smarty compile config
fs.readFile(`${rootPath}/install-dev/data/xml/configuration.xml`, 'utf8', (err, content) => {
  if (err) throw err;
  fs.writeFileSync(
    `${rootPath}/install-dev/data/xml/configuration.xml`,
    content
      .replace(/name="PS_SMARTY_FORCE_COMPILE"(.*\n*[^/]*)?value>[\d]+/, 'name="PS_SMARTY_FORCE_COMPILE"$1value>0')
      .replace(/name="PS_SMARTY_CONSOLE"(.*\n*[^/]*)?value>[\d]+/, 'name="PS_SMARTY_CONSOLE"$1value>0')
  );
});


// Update readme.txt
[
  '/docs/readme_de.txt',
  '/docs/readme_en.txt',
  '/docs/readme_es.txt',
  '/docs/readme_fr.txt',
  '/docs/readme_it.txt',
].forEach((filePath) => {
  const fullPath = rootPath + filePath;
  fs.readFile(fullPath, 'utf8', (err, content) => {
    if (err) throw err;
    fs.writeFileSync(
      fullPath,
      content
        .replace(/NAME: Prestashop ([0-9.]*)/, `NAME: Prestashop ${argv.version}`)
        .replace(/VERSION: ([0-9.]*)/, `VERSION: ${argv.version}`)
    );
  });
});


fs.readFile(`${rootPath}/install-dev/install_version.php`, 'utf8', (err, content) => {
  if (err) throw err;

  fs.writeFileSync(
    `${rootPath}/install-dev/install_version.php`,
    content
      .replace(/_PS_INSTALL_VERSION_', '(.*)'\)/, `_PS_INSTALL_VERSION_', '${argv.version}')`)
  );
});

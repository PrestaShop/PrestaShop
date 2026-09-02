/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
/* eslint-disable indent,comma-dangle */

/**
 * Three mode available:
 *  build = production mode
 *  dev = development mode
 */
const prod = require('./.webpack/prod.js');
const dev = require('./.webpack/dev.js');

module.exports = (env, argv) => (argv !== undefined && (argv.mode === 'production' || !argv.mode) ? prod() : dev());

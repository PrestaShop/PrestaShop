/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
Modernizr.load([
  {
    test: window.matchMedia,
    nope: [`${baseAdminDir}themes/default/js/vendor/matchMedia.js`, `${baseAdminDir}themes/default/js/vendor/matchMedia.addListener.js`],
  },
  `${baseAdminDir}themes/default/js/vendor/enquire.min.js`,
  `${baseAdminDir}themes/default/js/bundle/utils/animations.js`,
  `${baseAdminDir}themes/default/js/bundle/components/navbar-transition-handler.js`,
  `${baseAdminDir}themes/default/js/admin-theme.js`,
]);

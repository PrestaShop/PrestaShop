/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Send a Post Request to reset search Action.
 */

const {$} = window;

const init = function resetSearch(url, redirectUrl) {
  $.post(url).then(() => window.location.assign(redirectUrl));
};

export default init;

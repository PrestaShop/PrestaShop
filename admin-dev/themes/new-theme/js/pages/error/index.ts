/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

// jQuery is not available in this page
// so we use plain JS to to listen on button click
// which then goes back in browser history
(() => {
  const backBtn = document.querySelector('.js-go-back-btn');

  if (backBtn) {
    backBtn.addEventListener('click', () => window.history.back());
  }
})();

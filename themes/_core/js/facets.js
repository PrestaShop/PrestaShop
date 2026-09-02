/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';

let currentRequest = null;

function updateResults(data) {
  prestashop.emit('updateProductList', data);
  window.history.pushState(data, document.title, data.current_url);
}

function handleError(xhr, textStatus) {
  if (textStatus === 'abort') {
    return false;
  }
  // TODO: feedback
  return true;
}

function cleanRequest(xhr) {
  if (currentRequest === xhr) {
    currentRequest = null;
  }
}

function makeQuery(url) {
  if (currentRequest) {
    currentRequest.abort();
  }

  // We need to add a parameter to the URL
  // to make it different from the one we're on,
  // otherwise when you do "duplicate tab" under chrome
  // it mixes up the cache between the AJAX request (that
  // returns JSON) and the non-AJAX request (that returns
  // HTML) and you just get a mess of JSON on the duplicated tab.
  const separator = url.indexOf('?') >= 0 ? '&' : '?';
  const slightlyDifferentURL = `${url + separator}from-xhr`;

  currentRequest = $.ajax({
    url: slightlyDifferentURL,
    dataType: 'json',
    success: updateResults,
    error: handleError,
    complete: cleanRequest,
  });
}

$(() => {
  prestashop.on('updateFacets', (param) => {
    makeQuery(param);
  });
});

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';

let currentRequest = null;

function updateResults(data) {
  prestashop.emit('updateProductList', data);

  // WHY: the entry the visitor landed on carries no history state, so a later back
  // navigation to it fires popstate with a null state. Themes restore the listing from
  // state.current_url, so with no state nothing happens: the address bar returns to the
  // unfiltered listing while the page keeps showing the filtered one. Seeding that entry
  // before the first push makes it restore like every other entry. Whatever a module already
  // put on that entry is kept, since the state object is shared.
  if (!window.history.state || !window.history.state.current_url) {
    window.history.replaceState({...window.history.state, current_url: window.location.href}, document.title);
  }

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

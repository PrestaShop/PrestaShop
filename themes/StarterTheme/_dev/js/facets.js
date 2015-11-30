import $ from 'jquery';

let pendingQuery = false;

function updateDOM ({products, ps_search_facets}) {
    $('#products').replaceWith(products);
    $('#search_filters').replaceWith(ps_search_facets);
}

function updateResults (data) {
    pendingQuery = false;
    updateDOM(data);
    window.history.pushState(data, undefined, data.current_url);
    window.onpopstate = function (e) {
      updateDOM(e.state);
    };
}

function handleError () {
    // TODO: feedback
    pendingQuery = false;
}

function makeQuery (url) {
    if (pendingQuery) {
        // wait for current results
    } else {
        $
          .get(url, null, null, 'json')
          .then(updateResults)
          .fail(handleError)
        ;
    }
}

$(document).ready(function () {
    $('body').on('change', '#search_filters input[data-search-url]', function () {
        makeQuery(event.target.dataset.searchUrl);
    });

    $('body').on('click', '.js-search-link', function () {
        event.preventDefault();
        makeQuery($(event.target).closest('a').get(0).href);
    });
});

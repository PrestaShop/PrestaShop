import $ from 'jquery';

let pendingQuery = false;

function updateDOM ({rendered_products, rendered_facets}) {
    $('#products').replaceWith(rendered_products);
    $('#search_filters').replaceWith(rendered_facets);
}

const onpopstate = e => {
    if (e.state && e.state.rendered_products) {
        updateDOM(e.state);
    }
};

function updateResults (data) {
    pendingQuery = false;
    updateDOM(data);
    window.history.pushState(data, undefined, data.current_url);
    window.addEventListener('popstate', onpopstate);
    prestashop.emit('facetsUpdated');
}

function handleError () {
    // TODO: feedback
    pendingQuery = false;
}

function makeQuery (url) {
    if (pendingQuery) {
        // wait for current results
    } else {

        // We need to add a parameter to the URL
        // to make it different from the one we're on,
        // otherwise when you do "duplicate tab" under chrome
        // it mixes up the cache between the AJAX request (that
        // returns JSON) and the non-AJAX request (that returns
        // HTML) and you just get a mess of JSON on the duplicated tab.

        const slightlyDifferentURL = [
            url,
            url.indexOf('?') >= 0 ? '&' : '?',
            'from-xhr'
        ].join('');

        $
          .get(slightlyDifferentURL, null, null, 'json')
          .then(updateResults)
          .fail(handleError)
        ;
    }
}

$(document).ready(function () {
    prestashop.on('updateFacets', (param) => {
      makeQuery(param);
    });
});

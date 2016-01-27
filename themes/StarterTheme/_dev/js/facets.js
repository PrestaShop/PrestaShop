import $ from 'jquery';

let pendingQuery = false;

function updateDOM ({rendered_products, ps_search_facets}) {
    $('#products').replaceWith(rendered_products);
    $('#search_filters').replaceWith(ps_search_facets);
}

const onpopstate = e => {
    if (e.state && e.state.rendered_products) {
        updateDOM(e.state);
    }
}

function updateResults (data) {
    pendingQuery = false;
    updateDOM(data);
    window.history.pushState(data, undefined, data.current_url);
    window.addEventListener('popstate', onpopstate);
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

    $('body').on('change', '#search_filters select', function () {
        const form = $(event.target).closest('form');
        makeQuery('?' + form.serialize());
    });
});

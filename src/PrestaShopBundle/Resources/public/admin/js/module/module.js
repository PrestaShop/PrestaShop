$(document).ready(function() {

  console.log('Page Module inited');

  $('.module-grid').isotope({
    itemSelector: '.module-grid-item',
    layoutMode: 'fitColumns',
    fitColumns: {
          gutter: 10,
        },
  });

});

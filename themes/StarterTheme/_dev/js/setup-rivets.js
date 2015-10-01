import rivets from 'rivets';
import $ from 'jquery';

rivets.configure({
  prefix: 'ps',
  templateDelimiters: ['[[', ']]'],
});

rivets.formatters.propertyList = function(obj) {
  var key, _results;
  _results = [];
  for (key in obj) {
    _results.push(
      obj[key]
    );
  }
  return _results;
};

rivets.formatters.customerAddress = function(obj) {
  return prestashop.customer.addresses[obj].formatted;
};

$(document).ready(function(){
  window.view = rivets.bind($('body'),{

    prestashop:prestashop,

  });
});

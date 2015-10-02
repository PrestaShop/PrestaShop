import rivets from 'rivets';
import $ from 'jquery';
import prestashop from 'prestashop';

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
  var address = prestashop.customer.addresses[obj];
  if (address) {
    return address.formatted;
  } else {
    return undefined;
  }
};

$(document).ready(function(){
  window.view = rivets.bind($('body'),{

    prestashop:prestashop,

  });
});

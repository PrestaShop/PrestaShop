rivets.configure({
  prefix: 'ps',
  templateDelimiters: ['[[', ']]'],
})

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

$(document).ready(function(){
  window.view = rivets.bind($('body'),{

    prestashop:prestashop,

  });
});

import $ from 'jquery';
import Bloodhound from 'typeahead.js';

export default function() {
  $(document).ready(function() {
    let formId = `#${$('.autocomplete-search').data('formid')}-data .delete`;
    let autocompleteSource = `${$('.autocomplete-search').data('formid')}_source`;

    $(document).on('click', formId, (e) => {
      e.preventDefault();

      window.modalConfirmation.create(window.translate_javascripts['Are you sure to delete this?'], null, {
        onContinue: () => {
          $(e.target).parent().remove();
        }
      }).show();
    });

    document[autocompleteSource] = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      identify: function(obj) {
        return obj[$('.autocomplete-search').data('mappingvalue')];
      },
      remote: {
        url: $('.autocomplete-search').data('remoteurl'),
        cache: false,
        wildcard: '%QUERY',
        transform: function(response) {
          if (!response) {
            return [];
          }
          return response;
        }
      }
    });

    //define typeahead
    $('#' + $('.autocomplete-search').data('formid')).typeahead({
      limit: 20,
      minLength: 2,
      highlight: true,
      cache: false,
      hint: false,
    }, {
      display: $('.autocomplete-search').data('mappingname'),
      source: this[$('.autocomplete-search').data('formid') + '_source'],
      limit: 30,
      templates: {
        suggestion: function(item) {
          return '<div><img src="' + item.image + '" style="width:50px" /> ' + item.name + '</div>';
        }
      }
    }).bind('typeahead:select', function(e, suggestion) {
      //if collection length is up to limit, return

      let formIdItem = `#${$('.autocomplete-search').data('formid')}-data li)`;

      if ($('.autocomplete-search').data('limit') !== 0 && formIdItem.length >= $('.autocomplete-search').data('limit')) {
        return false;
      }

      var value = suggestion[$('.autocomplete-search').data('mappingvalue')];
      if (suggestion.id_product_attribute) {
        value = value + ',' + suggestion.id_product_attribute;
      }

      let tplcollection = $('#tplcollection-' + $('.autocomplete-search').data('formid'));
      let tplcollectionHtml = tplcollection.html().replace('%s', suggestion[$('.autocomplete-search').data('mappingname')]);

      var html = `<li class="card">
                  <img class="image" src="${suggestion.image}" />
                  ${tplcollectionHtml}
                  <input type="hidden" name="${$('.autocomplete-search').data('fullname')}[data][]" value="${value}" />
                  </li>`;

      $('#' + $('.autocomplete-search').data('formid') + '-data').append(html);

    }).bind('typeahead:close', function(e) {
      $(e.target).val('');
    });
  });
}

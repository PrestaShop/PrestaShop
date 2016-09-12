import $ from 'jquery';
import Bloodhound from 'typeahead.js';

export default function() {
  $(document).ready(function() {
    $('.autocomplete-search').each(function() {
        let autocompleteObject = $(this);
        let autocompleteFormId = autocompleteObject.attr('data-formid');
        let formId = `#${autocompleteFormId}-data .delete`;
        let autocompleteSource = `${autocompleteFormId}_source`;

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
            return obj[autocompleteObject.attr('data-mappingvalue')];
          },
          remote: {
            url: autocompleteObject.attr('data-remoteurl'),
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
        $('#' + autocompleteFormId).typeahead({
          limit: 20,
          minLength: 2,
          highlight: true,
          cache: false,
          hint: false,
        }, {
          display: autocompleteObject.attr('data-mappingname'),
          source: document[autocompleteFormId + '_source'],
          limit: 30,
          templates: {
            suggestion: function(item) {
              return '<div><img src="' + item.image + '" style="width:50px" /> ' + item.name + '</div>';
            }
          }
        }).bind('typeahead:select', function(e, suggestion) {
          //if collection length is up to limit, return

          let formIdItem = $(`#${autocompleteFormId}-data li`);
          let autocompleteFormLimit = parseInt(autocompleteObject.attr('data-limit'));

          if (autocompleteFormLimit !== 0 && formIdItem.length >= autocompleteFormLimit) {
            return false;
          }

          var value = suggestion[autocompleteObject.attr('data-mappingvalue')];
          if (suggestion.id_product_attribute) {
            value = value + ',' + suggestion.id_product_attribute;
          }

          let tplcollection = $('#tplcollection-' + autocompleteFormId);
          let tplcollectionHtml = tplcollection.html().replace('%s', suggestion[autocompleteObject.attr('data-mappingname')]);

          var html = `<li class="media">
                      <div class="media-left">
                      <img class="media-object image" src="${suggestion.image}" />
                      </div>
                      <div class="media-body media-middle">
                      ${tplcollectionHtml}
                      </div>
                      <input type="hidden" name="${autocompleteObject.attr('data-fullname')}[data][]" value="${value}" />
                      </li>`;

          $('#' + autocompleteFormId + '-data').append(html);

        }).bind('typeahead:close', function(e) {
          $(e.target).val('');
        });
    });
  });
}

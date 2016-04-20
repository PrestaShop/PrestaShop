import $ from 'jquery';

export default function() {
  $( document ).ready(function() {
    $(document).on( 'click', '#' + $('.autocomplete-search').data('formid') + '-data .delete', function(e) {
        e.preventDefault();
        var _this = $(this);

        modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
            onContinue: function(){
                _this.parent().remove();
            }
        }).show();
    });

    //define source
    this[$('.autocomplete-search').data('formid') + '_source'] = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        identify: function(obj) {
            return obj[$('.autocomplete-search').data('mappingvalue')];
        },
        remote: {
            url: $('.autocomplete-search').data('remoteurl'),
            cache: false,
            wildcard: '%QUERY',
            transform: function(response){
                if(!response){
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
            suggestion: function(item){
                return '<div><img src="'+ item.image +'" style="width:50px" /> '+ item.name +'</div>'
            }
        }
    }).bind('typeahead:select', function(ev, suggestion) {
        //if collection length is up to limit, return
        if($('.autocomplete-search').data('limit') != 0 && $('#' + $('.autocomplete-search').data('formid') + '-data li').length >= $('.autocomplete-search').data('limit')){
            return;
        }

        var value = suggestion[$('.autocomplete-search').data('mappingvalue')];
        if (suggestion.id_product_attribute) {
            value = value+','+suggestion.id_product_attribute;
        }

        var html = '<li class="card">';
        html += '<img class="image" src="'+ suggestion.image +'" /> ';
        html += $('#tplcollection-' + $('.autocomplete-search').data('formid')).html().replace('%s', suggestion[$('.autocomplete-search').data('mappingname')]);
        html += '<input type="hidden" name="' + $('.autocomplete-search').data('fullname') + '[data][]" value="' + value + '" />';
        html += '</li>';

        $('#' + $('.autocomplete-search').data('formid') + '-data').append(html);

    }).bind('typeahead:close', function(ev) {
        $(ev.target).val('');
    });
  });
}

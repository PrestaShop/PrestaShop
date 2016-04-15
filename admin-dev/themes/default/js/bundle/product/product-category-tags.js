/**
 * Product categories Tags management
 */
var productCategoriesTags = (function () {
  return {
    'init': function () {
      selectedCategories = this.getTags();
      selectedCategories.forEach(this.createTag);

      // add tags management
      this.manageTagsOnInput();
      this.manageTagsOnTags();

      // add search box
      this.initSearchBox();
    },
    'removeTag': function (categoryId) {
      $('span[data-id^="' + categoryId + '"]').parent().remove();

      return true;
    },
    'getTags': function () {
      var categoriesForm = $('#form_step1_categories');
      var inputs = categoriesForm.find('label > input:checked').toArray();

      var tags = [];
      var that = this;
      inputs.forEach(function getLabels(input) {
        var tree = that.getTree();
        var tag = {
          'name': input.parentNode.innerText,
          'id': input.value,
        };
        tree.forEach(function getCategories(_category) {
          if (_category.id == tag.id) {
            tag.breadcrumb = _category.breadcrumb;
          }
        });

        tags.push(tag);
      });

      return tags;
    },
    'manageTagsOnInput': function () {
      var categoriesForm = $('#form_step1_categories');
      var that = this;
      categoriesForm.on('click', 'input', function () {
        var input = $(this);
        if (input.prop('checked') === false) {
          that.removeTag($(this).val());
        } else {
          var tag = {
            'name': input.parent().text(),
            'id': input.val(),
            'breadcrumb': ''
          };

          that.createTag(tag);
        }
      });

      return true;
    },
    'manageTagsOnTags': function () {
      var categoriesForm = $('#form_step1_categories');
      var tagsContainer = $('#ps_categoryTags');
      var that = this;

      tagsContainer.on('click', 'a.pstaggerClosingCross', function (event) {
        event.preventDefault();
        var id = $(this).data('id');
        that.removeTag(id);
        categoriesForm.find('input[value="' + id + '"]').prop('checked', false);
        tagsContainer.focus();
      });

      return true;
    },
    'getTree': function () {
      var tree = JSON.parse($('#ps_categoryTree').html());

      return tree;
    },
    'createTag': function (category) {

      if (category.breadcrumb == '') {
        var tree = this.getTree();
        tree.forEach(function getCategories(_category) {
          if (_category.id == category.id) {
            category.breadcrumb = _category.breadcrumb;
          }
        });
      }

      $('#ps_categoryTags').append('<span class="pstaggerTag">' +
        '<span data-id="' + category.id + '" title="' + category.breadcrumb + '">' + category.name + '</span>' +
        '<a class="pstaggerClosingCross" href="#" data-id="' + category.id + '">x</a>' +
        '</span>');

      return true;
    },
    'getNameFromBreadcrumb': function (name) {

      if (name.indexOf('&gt;') !== -1) {
        return name.substring(name.lastIndexOf('&gt') + 4); // remove "&gt; "
      }

      return name;
    },
    'initSearchBox': function () {
      var searchBox = $('#ps-select-product-category');
      var tree = this.getTree();
      var tags = [];
      var that = this;
      tree.forEach(function buildTags(tagObject){
        tags.push({
          label: tagObject.breadcrumb,
          value: tagObject.id
        });
      });

      searchBox.autocomplete({
        source: tags,
        minChars: 2,
        autoFill: true,
        max:20,
        matchContains: true,
        mustMatch:false,
        scroll:false,
        select: function(event, ui) {
          event.preventDefault();
          var label = ui.item.label;
          var categoryName = that.getNameFromBreadcrumb(label);
          var categoryId = ui.item.value;
          that.createTag({
            'name': categoryName,
            'id': categoryId,
            'breadcrumb': label
          });
          var categoriesForm = $('#form_step1_categories');
          categoriesForm.find('input[value="' + categoryId + '"]').attr('checked', 'checked');
          $(this).val('');
        }
      }).data('ui-autocomplete')._renderItem = function(ul, item) {
        return $('<li>')
          .data('ui-autocomplete-item', item)
          .append('<a>'+item.label+'</a>')
          .appendTo(ul);
      };
    }
  };
})();

BOEvent.on("Product Categories Management started", function initTagsManagement() {
  productCategoriesTags.init();
}, "Back office");

/**
 * Product categories Tags management
 */
var productCategoriesTags = (function () {
  var defaultCategoryForm = $('#form_step1_id_category_default');
  var categoriesForm = $('#form_step1_categories');
  var tagsContainer = $('#ps_categoryTags');

  return {
    'init': function () {
      selectedCategories = this.getTags();
      selectedCategories.forEach(this.createTag);

      // add tags management
      this.manageTagsOnInput();
      this.manageTagsOnTags();

      // add default category management
      this.checkDefaultCategory();

      // add search box
      this.initSearchBox();
    },
    'removeTag': function (categoryId) {
      $('span[data-id^="' + categoryId + '"]').parent().remove();

      return true;
    },
    'getTags': function () {
      var categoriesForm = $('#form_step1_categories');
      var inputs = categoriesForm.find('label > input[type=checkbox]:checked').toArray();

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
      categoriesForm.on('change', 'input[type=checkbox]', function (event) {
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
      var that = this;

      tagsContainer.on('click', 'a.pstaggerClosingCross', function (event) {
        event.preventDefault();
        var id = $(this).data('id');
        that.removeTag(id);
        categoriesForm.find('input[value="' + id + '"].category').prop('checked', false);
        tagsContainer.focus();
      });

      return true;
    },
    'checkDefaultCategory': function (categoryId) {
      var categoriesForm = $('#form_step1_categories');
      var selector = 'input[value="'+categoryId+'"].default-category';
      categoriesForm.find(selector).prop('checked', true);
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

      var isTagExist = tagsContainer.find('span[data-id='+ category.id +']');

      if(0 == isTagExist.length) {
        tagsContainer.append('<span class="pstaggerTag">' +
          '<span data-id="' + category.id + '" title="' + category.breadcrumb + '">' + category.name + '</span>' +
          '<a class="pstaggerClosingCross" href="#" data-id="' + category.id + '">x</a>' +
          '</span>')
        ;

        var optionId = '#form_step1_id_category_default_' + category.id;
        if (0 == $(optionId).length) {
          defaultCategoryForm.append('<div class="radio">' +
            '<label class="required">' +
            '<input type="radio"' + 'id="form_step1_id_category_default_' + category.id + '" name="form[step1][id_category_default]" required="required" value="' + category.id + '">' +
            category.name +'</label>' +
            '</div>');
        }
      }


      return true;
    },
    'getNameFromBreadcrumb': function (name) {

      if (name.indexOf('&gt;') !== -1) {
        return name.substring(name.lastIndexOf('&gt') + 4); // remove "&gt; "
      }

      return name;
    },
    'initSearchBox': function () {
      var searchCategorySelector = '#ps-select-product-category';
      var searchBox = $(searchCategorySelector);
      var tree = this.getTree();
      var tags = [];
      var that = this;
      let searchResultMsg = '';
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
        focus: function(event, ui) {
          event.preventDefault();
          let $this = $(this);
          $this.val(that.getNameFromBreadcrumb(ui.item.label));
          searchResultMsg = $this.parent().find('[role=status]').text();
        },
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
          categoriesForm.find('input[value="' + categoryId + '"].category').prop('checked', true);
          $(this).val('');
        }
      }).data('ui-autocomplete')._renderItem = function(ul, item) {
        return $('<li>')
          .data('ui-autocomplete-item', item)
          .append('<a>'+item.label+'</a>')
          .appendTo(ul);
      };

      searchBox.parent().find('[role=status]').on('DOMSubtreeModified', function () {
        let $this = $(this);
        if ($.isNumeric($this.text()) && searchResultMsg !== '' && searchBox.val() !== '') {
          $this.text(searchResultMsg);
        }
      });

      $('body').on('focusout', searchCategorySelector, function (event) {
        var $searchInput = $(event.currentTarget);
        if (0 === $searchInput.val().length ) {
          $searchInput.parent().find('[role=status]').text('');
          searchResultMsg = '';
        }
      });
    }
  };
})();

BOEvent.on("Product Categories Management started", function initTagsManagement() {
  productCategoriesTags.init();
}, "Back office");

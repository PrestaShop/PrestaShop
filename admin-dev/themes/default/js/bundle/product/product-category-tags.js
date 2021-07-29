/**
 * Product categories Tags management
 */
const productCategoriesTags = (function () {
  const defaultCategoryForm = $('#form_step1_id_category_default');
  const categoriesForm = $('#form_step1_categories');
  const tagsContainer = $('#ps_categoryTags');

  return {
    init() {
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
    removeTag(categoryId) {
      $(`span[data-id^="${categoryId}"]`).parent().remove();

      return true;
    },
    getTags() {
      const firstStepCategoriesForm = $('#form_step1_categories');
      const inputs = firstStepCategoriesForm.find('label > input[type=checkbox]:checked').toArray();

      const tags = [];
      const that = this;
      inputs.forEach((input) => {
        const tree = that.getTree();
        const tag = {
          name: input.parentNode.innerText,
          id: input.value,
        };
        tree.forEach((_category) => {
          if (_category.id === tag.id) {
            tag.breadcrumb = _category.breadcrumb;
          }
        });

        tags.push(tag);
      });

      return tags;
    },
    manageTagsOnInput() {
      const firstStepCategoriesForm = $('#form_step1_categories');
      const that = this;
      firstStepCategoriesForm.on('change', 'input[type=checkbox]', function () {
        const input = $(this);

        if (input.prop('checked') === false) {
          that.removeTag($(this).val());
        } else {
          const tag = {
            name: input.parent().text(),
            id: input.val(),
            breadcrumb: '',
          };

          that.createTag(tag);
        }
      });

      return true;
    },
    manageTagsOnTags() {
      const that = this;

      tagsContainer.on('click', 'a.pstaggerClosingCross', function (event) {
        event.preventDefault();
        const id = $(this).data('id');
        that.removeTag(id);
        categoriesForm.find(`input[value="${id}"].category`).prop('checked', false);
        tagsContainer.focus();
      });

      return true;
    },
    checkDefaultCategory(categoryId) {
      const firstStepCategoriesForm = $('#form_step1_categories');
      const selector = `input[value="${categoryId}"].default-category`;
      firstStepCategoriesForm.find(selector).prop('checked', true);
    },
    getTree() {
      const tree = JSON.parse($('#ps_categoryTree').html());

      return tree;
    },
    createTag(category) {
      if (category.breadcrumb === '') {
        const tree = this.getTree();
        tree.forEach((_category) => {
          if (_category.id === category.id) {
            category.breadcrumb = _category.breadcrumb;
          }
        });
      }

      const isTagExist = tagsContainer.find(`span[data-id=${category.id}]`);

      if (isTagExist.length === 0) {
        tagsContainer.append(`${'<span class="pstaggerTag">'
          + '<span data-id="'}${category.id}" title="${category.breadcrumb}">${category.name}</span>`
          + `<a class="pstaggerClosingCross" href="#" data-id="${category.id}">x</a>`
          + '</span>');
        const optionId = `#form_step1_id_category_default_${category.id}`;

        if ($(optionId).length === 0) {
          defaultCategoryForm.append(`${'<div class="radio">'
            + '<label class="required">'
            // eslint-disable-next-line
            + '<input type="radio"' + 'id="form_step1_id_category_default_'}${category.id}" name="form[step1][id_category_default]" required="required" value="${category.id}">${
            category.name}</label>`
            + '</div>');
        }
      }

      return true;
    },
    getNameFromBreadcrumb(name) {
      if (name.indexOf('&gt;') !== -1) {
        return name.substring(name.lastIndexOf('&gt') + 4); // remove "&gt; "
      }

      return name;
    },
    initSearchBox() {
      const searchCategorySelector = '#ps-select-product-category';
      const searchBox = $(searchCategorySelector);
      const tree = this.getTree();
      const tags = [];
      const that = this;
      let searchResultMsg = '';
      tree.forEach((tagObject) => {
        tags.push({
          label: tagObject.breadcrumb,
          value: tagObject.id,
        });
      });

      // eslint-disable-next-line
      searchBox.autocomplete({
        source: tags,
        minChars: 2,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: false,
        scroll: false,
        focus(event, ui) {
          event.preventDefault();
          const $this = $(this);
          $this.val(that.getNameFromBreadcrumb(ui.item.label));
          searchResultMsg = $this.parent().find('[role=status]').text();
        },
        select(event, ui) {
          event.preventDefault();
          const {label} = ui.item;
          const categoryName = that.getNameFromBreadcrumb(label);
          const categoryId = ui.item.value;

          that.createTag({
            name: categoryName,
            id: categoryId,
            breadcrumb: label,
          });
          const firstStepCategoriesForm = $('#form_step1_categories');
          firstStepCategoriesForm.find(`input[value="${categoryId}"].category`).prop('checked', true);
          $(this).val('');
        },
      }).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
          .data('ui-autocomplete-item', item)
          .append(`<a>${item.label}</a>`)
          .appendTo(ul);
      };

      searchBox.parent().find('[role=status]').on('DOMSubtreeModified', function () {
        const $this = $(this);

        if ($.isNumeric($this.text()) && searchResultMsg !== '' && searchBox.val() !== '') {
          $this.text(searchResultMsg);
        }
      });

      $('body').on('focusout', searchCategorySelector, (event) => {
        const $searchInput = $(event.currentTarget);

        if ($searchInput.val().length === 0) {
          $searchInput.parent().find('[role=status]').text('');
          searchResultMsg = '';
        }
      });
    },
  };
}());

window.productCategoriesTags = productCategoriesTags;

BOEvent.on('Product Categories Management started', () => {
  productCategoriesTags.init();
}, 'Back office');

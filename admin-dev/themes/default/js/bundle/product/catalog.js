/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/* eslint-disable no-unused-vars, no-unreachable */

const {$} = window;

$(document).ready(() => {
  const form = $('form#product_catalog_list');

  /*
   * Tree behavior: collapse/expand system and radio button change event.
   */
  $('div#product_catalog_category_tree_filter').categorytree();
  $('div#product_catalog_category_tree_filter div.radio > label > input:radio').change(function () {
    if ($(this).is(':checked')) {
      $('form#product_catalog_list input[name="filter_category"]').val($(this).val());
      $('form#product_catalog_list').submit();
    }
  });
  $('div#product_catalog_category_tree_filter ~ div button, div#product_catalog_category_tree_filter ul')
    .on('click', () => {
      categoryFilterButtons();
    });
  categoryFilterButtons();

  /*
   * Click on a column header ordering icon to change orderBy / orderWay (location.href redirection)
   */
  $('[psorderby][psorderway]', form).click(function () {
    const orderBy = $(this).attr('psorderby');
    const orderWay = $(this).attr('psorderway');
    productOrderTable(orderBy, orderWay);
  });

  /*
   * Checkboxes behavior with bulk actions
   */
  $('input:checkbox[name="bulk_action_selected_products[]"]', form).change(() => {
    updateBulkMenu();
  });

  /*
   * Filter columns inputs behavior
   */
  $('tr.column-filters input:text, tr.column-filters select', form).on('change input', () => {
    productCatalogFilterChanged = true;
    updateFilterMenu();
  });

  /*
   * Sortable case when ordered by position ASC
   */

  $('body').on('mousedown', 'tbody.sortable [data-uniturl] td.placeholder', function () {
    const trParent = $(this).closest('tr');
    trParent.find('input:checkbox[name="bulk_action_selected_products[]"]').attr('checked', true);
  });

  $('tbody.sortable', form).sortable({
    placeholder: 'placeholder',
    update(event, ui) {
      const positionSpan = $('span.position', ui.item)[0];
      $(positionSpan).css('color', 'red');
      bulkProductEdition(event, 'sort');
    },
  });

  /*
   * Form submit pre action
   */
  form.submit(function (e) {
    e.preventDefault();
    $('#filter_column_id_product', form).val($('#filter_column_id_product', form).attr('sql'));
    $('#filter_column_price', form).val($('#filter_column_price', form).attr('sql'));
    $('#filter_column_sav_quantity', form).val($('#filter_column_sav_quantity', form).attr('sql'));
    productCatalogFilterChanged = false;
    this.submit();
    return false;
  });

  /*
   * Send to SQL manager button on modal
   */
  $('#catalog_sql_query_modal button[value="sql_manager"]').on('click', () => {
    sendLastSqlQuery(createSqlQueryName());
  });

  updateBulkMenu();
  updateFilterMenu();

  /** create keyboard event for save & new */
  jwerty.key('ctrl+P', (e) => {
    e.preventDefault();
    const url = $('form#product_catalog_list').attr('newproducturl');
    window.location.href = url;
  });
});

function productOrderTable(orderBy, orderWay) {
  const form = $('form#product_catalog_list');
  const url = form.attr('orderingurl').replace(/name/, orderBy).replace(/asc/, orderWay);
  window.location.href = url;
}

// eslint-disable-next-line
function productOrderPrioritiesTable() {
  window.location.href = $('form#product_catalog_list').attr('orderingurl');
}

function updateBulkMenu() {
  // eslint-disable-next-line
  const selectedCount = $('form#product_catalog_list input:checked[name="bulk_action_selected_products[]"][disabled!="disabled"]').length;
  $('#product_bulk_menu').prop('disabled', (selectedCount === 0));
}

let productCatalogFilterChanged = false;
function updateFilterMenu() {
  const columnFilters = $('#product_catalog_list').find('tr.column-filters');
  let count = columnFilters.find('option:selected[value!=""]').length;
  columnFilters.find('input[type="text"][sql!=""][sql], input[type="text"]:visible').each(function () {
    if ($(this).val() !== '') {
      count += 1;
    }
  });
  const filtersNotUpdatedYet = (count === 0 && productCatalogFilterChanged === false);
  $('button[name="products_filter_submit"]').prop('disabled', filtersNotUpdatedYet);
  $('button[name="products_filter_reset"]').toggle(!filtersNotUpdatedYet);
}

function productCategoryFilterReset(div) {
  $('#product_categories').categorytree('unselect');
  $('#product_catalog_list input[name="filter_category"]').val('');
  $('#product_catalog_list').submit();
}

function productCategoryFilterExpand(div, btn) {
  $('#product_categories').categorytree('unfold');
}

function productCategoryFilterCollapse(div, btn) {
  $('#product_categories').categorytree('fold');
}

function categoryFilterButtons() {
  const catTree = $('#product_catalog_category_tree_filter');
  const catTreeSiblingDivs = $('#product_catalog_category_tree_filter ~ div');
  const catTreeList = catTree.find('ul ul');
  catTreeSiblingDivs.find('button[name="product_catalog_category_tree_filter_collapse"]')
    .toggle(!catTreeList.filter(':visible').length);
  catTreeSiblingDivs.find('button[name="product_catalog_category_tree_filter_expand"]')
    .toggle(!catTreeList.filter(':hidden').length);
  catTreeSiblingDivs.find('button[name="product_catalog_category_tree_filter_reset"]')
    .toggle(!catTree.find('ul input:checked').length);
}

function productColumnFilterReset(tr) {
  $('input:text', tr).val('');
  $('select option:selected', tr).prop('selected', false);
  $('#filter_column_price', tr).attr('sql', '');
  $('#filter_column_sav_quantity', tr).attr('sql', '');
  $('#filter_column_id_product', tr).attr('sql', '');
  $('#product_catalog_list').submit();
}

function bulkModalAction(allItems, postUrl, redirectUrl, action) {
  const itemsCount = allItems.length;
  let currentItemIdx = 0;

  if (itemsCount < 1) {
    return;
  }

  const targetModal = $(`#catalog_${action}_modal`);
  targetModal.modal('show');

  const details = targetModal.find(`#catalog_${action}_progression .progress-details-text`);
  const progressBar = targetModal.find(`#catalog_${action}_progression .progress-bar`);
  const failure = targetModal.find(`#catalog_${action}_failure`);

  // re-init popup
  details.html(details.attr('default-value'));

  progressBar.css('width', '0%');
  progressBar.find('span').html('');
  progressBar.removeClass('progress-bar-danger');
  progressBar.addClass('progress-bar-success');

  failure.hide();

  // call in ajax. Recursive with inner function
  const bulkCall = function (items, successCallback, errorCallback) {
    if (items.length === 0) {
      return;
    }
    const item0 = $(items.shift()).val();
    currentItemIdx += 1;

    details.html(`${details.attr('default-value').replace(/\.\.\./, '')} (#${item0})`);
    $.ajax({
      type: 'POST',
      url: postUrl,
      data: {bulk_action_selected_products: [item0]},
      success(data, status) {
        // eslint-disable-next-line
        progressBar.css('width', `${currentItemIdx * 100 / itemsCount}%`);
        progressBar.find('span').html(`${currentItemIdx} / ${itemsCount}`);

        if (items.length > 0) {
          bulkCall(items, successCallback, errorCallback);
        } else {
          successCallback();
        }
      },
      error: errorCallback,
      dataType: 'json',
    });
  };

  bulkCall(allItems.toArray(), () => {
    window.location.href = redirectUrl;
  }, () => {
    progressBar.removeClass('progress-bar-success');
    progressBar.addClass('progress-bar-danger');
    failure.show();
    window.location.href = redirectUrl;
  });
}

function bulkProductAction(element, action) {
  const form = $('#product_catalog_list');
  let postUrl = '';
  let redirectUrl = '';
  let urlHandler = null;

  const items = $('input:checked[name="bulk_action_selected_products[]"]', form);

  if (items.length === 0) {
    return false;
  }
  urlHandler = $(element).closest('[bulkurl]');

  switch (action) {
    case 'delete_all':
      postUrl = urlHandler.attr('bulkurl').replace(/activate_all/, action);
      redirectUrl = urlHandler.attr('redirecturl');

      // Confirmation popup and callback...
      $('#catalog_deletion_modal').modal('show');
      $('#catalog_deletion_modal button[value="confirm"]').off('click');
      $('#catalog_deletion_modal button[value="confirm"]').on('click', () => {
        $('#catalog_deletion_modal').modal('hide');

        return bulkModalAction(items, postUrl, redirectUrl, action);
      });

      return true; // No break, but RETURN, to avoid code after switch block :)

    case 'activate_all':
      postUrl = urlHandler.attr('bulkurl');
      redirectUrl = urlHandler.attr('redirecturl');

      return bulkModalAction(items, postUrl, redirectUrl, action);

      break;

    case 'deactivate_all':
      postUrl = urlHandler.attr('bulkurl').replace(/activate_all/, action);
      redirectUrl = urlHandler.attr('redirecturl');

      return bulkModalAction(items, postUrl, redirectUrl, action);

      break;

    case 'duplicate_all':
      postUrl = urlHandler.attr('bulkurl').replace(/activate_all/, action);
      redirectUrl = urlHandler.attr('redirecturl');

      return bulkModalAction(items, postUrl, redirectUrl, action);

      break;

    // this case will brings to the next page
    case 'edition_next':
      redirectUrl = $(element).closest('[massediturl]').attr('redirecturlnextpage');
      // no break !

    // this case will post inline edition command
    // eslint-disable-next-line
    case 'edition':
      // eslint-disable-next-line
      let editionAction;
      // eslint-disable-next-line
      const bulkEditionSelector = '#bulk_edition_toolbar input:submit';

      if ($(bulkEditionSelector).length > 0) {
        editionAction = $(bulkEditionSelector).attr('editionaction');
      } else {
        editionAction = 'sort';
      }

      urlHandler = $('[massediturl]');
      postUrl = urlHandler.attr('massediturl').replace(/sort/, editionAction);
      if (redirectUrl === '') {
        redirectUrl = urlHandler.attr('redirecturl');
      }
      break;

    // unknown cases...
    default:
      return false;
  }

  if (postUrl !== '' && redirectUrl !== '') {
    // save action URL for redirection and update to post to bulk action instead
    // using form action URL allow to get route attributes and stay on the same page & ordering.
    const redirectionInput = $('<input>')
      .attr('type', 'hidden')
      .attr('name', 'redirect_url').val(redirectUrl);
    form.append($(redirectionInput));
    form.attr('action', postUrl);
    form.submit();
  }
  return false;
}

function unitProductAction(element, action) {
  const form = $('form#product_catalog_list');

  // save action URL for redirection and update to post to bulk action instead
  // using form action URL allow to get route attributes and stay on the same page & ordering.
  const urlHandler = $(element).closest('[data-uniturl]');
  const redirectUrlHandler = $(element).closest('[redirecturl]');
  const redirectionInput = $('<input>')
    .attr('type', 'hidden')
    .attr('name', 'redirect_url').val(redirectUrlHandler.attr('redirecturl'));

  // eslint-disable-next-line
  switch (action) {
    case 'delete':
      // Confirmation popup and callback...
      $('#catalog_deletion_modal').modal('show');
      $('#catalog_deletion_modal button[value="confirm"]').off('click');
      $('#catalog_deletion_modal button[value="confirm"]').on('click', () => {
        form.append($(redirectionInput));
        const url = urlHandler.attr('data-uniturl').replace(/duplicate/, action);
        form.attr('action', url);
        form.submit();

        $('#catalog_deletion_modal').modal('hide');
      });
      return;
    // Other cases, nothing to do, continue.
    // default:
  }

  form.append($(redirectionInput));
  const url = urlHandler.attr('data-uniturl').replace(/duplicate/, action);
  form.attr('action', url);
  form.submit();
}

function showBulkProductEdition(show) {
  // Paginator does not have a next page link : we are on the last page!
  if ($('a#pagination_next_url[href]').length === 0) {
    $('#bulk_edition_save_next').prop('disabled', true).removeClass('btn-primary');
    $('#bulk_edition_save_keep').attr('type', 'submit').addClass('btn-primary');
  }
  if (show) {
    $('#bulk_edition_toolbar').show();
  } else {
    $('#bulk_edition_toolbar').hide();
  }
}

function bulkProductEdition(element, action) {
  const form = $('form#product_catalog_list');

  // eslint-disable-next-line
  switch (action) {
    case 'sort':
      showBulkProductEdition(true);
      $('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form)
        .prop('disabled', true);
      $('#bulk_edition_toolbar input:submit').attr('editionaction', action);
      break;
    case 'cancel':
      // quantity inputs
      $('td.product-sav-quantity', form).each(function () {
        $(this).html($(this).attr('productquantityvalue'));
      });

      $('#bulk_edition_toolbar input:submit').removeAttr('editionaction');
      showBulkProductEdition(false);
      $('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form)
        .prop('disabled', false);
      break;
  }
}

function showLastSqlQuery() {
  $('#catalog_sql_query_modal_content textarea[name="sql"]').val($('tbody[last_sql]').attr('last_sql'));
  $('#catalog_sql_query_modal').modal('show');
}

function sendLastSqlQuery(name) {
  $('#catalog_sql_query_modal_content textarea[name="sql"]').val($('tbody[last_sql]').attr('last_sql'));
  $('#catalog_sql_query_modal_content input[name="name"]').val(name);
  $('#catalog_sql_query_modal_content').submit();
}

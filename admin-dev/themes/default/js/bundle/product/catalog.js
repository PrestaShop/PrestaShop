/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {
	var form = $('form#product_catalog_list');

	/*
	 * Tree behavior: collapse/expand system and radio button change event.
	 */
	$('div#product_catalog_category_tree_filter').categorytree();
	$('div#product_catalog_category_tree_filter div.radio > label > input:radio').change(function() {
		if ($(this).is(':checked')) {
			$('form#product_catalog_list input[name="filter_category"]').val($(this).val());
			$('form#product_catalog_list').submit();
		}
	});
	$('div#product_catalog_category_tree_filter ~ div button, div#product_catalog_category_tree_filter ul').on('click', function() {
		categoryFilterButtons();
	});
	categoryFilterButtons();

	/*
	 * Click on a column header ordering icon to change orderBy / orderWay (location.href redirection)
	 */
	$('[psorderby][psorderway]', form).click(function() {
		var orderBy = $(this).attr('psorderby');
		var orderWay = $(this).attr('psorderway');
		productOrderTable(orderBy, orderWay);
	});

	/*
	 * Checkboxes behavior with bulk actions
	 */
	$('input:checkbox[name="bulk_action_selected_products[]"]', form).change(function() {
		updateBulkMenu();
	});

	/*
	 * Filter columns inputs behavior
	 */
	$('tr.column-filters input:text, tr.column-filters select', form).change(function() {
		productCatalogFilterChanged = true;
		updateFilterMenu();
	});

	/*
	 * Sortable case when ordered by position ASC
	 */

	$("body").on("mousedown", "tbody.sortable [data-uniturl]", function () {
		$(this).find('input:checkbox[name="bulk_action_selected_products[]"]').attr("checked", true);
	});

	$('tbody.sortable', form).sortable({
		placeholder: 'placeholder',
		update: function(event, ui) {
			var positionSpan = $('span.position', ui.item)[0];
			$(positionSpan).css('color', 'red');
			bulkProductEdition(event, 'sort');
		}
	});

	/*
	 * Form submit pre action
	 */
	form.submit(function(e) {
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
	$('#catalog_sql_query_modal button[value="sql_manager"]').on('click', function() {
		sendLastSqlQuery(createSqlQueryName());
	});

	updateBulkMenu();
	updateFilterMenu();

	/** create keyboard event for save & new */
	jwerty.key('ctrl+P', function(e) {
		e.preventDefault();
		var url = $('form#product_catalog_list').attr('newproducturl');
		window.location.href = url;
	});
});

function productOrderTable(orderBy, orderWay) {
	var form = $('form#product_catalog_list');
	var url = form.attr('orderingurl').replace(/name/, orderBy).replace(/asc/, orderWay);
	window.location.href = url;
}

function productOrderPrioritiesTable() {
	var form = $('form#product_catalog_list');
	var url = form.attr('orderingurl').replace(/name/, 'position_ordering').replace(/desc/, 'asc');
	url = url.replace(/\/\d+\/\d+\/position_ordering\//, '/0/300/position_ordering/');
	window.location.href = url;
}

function updateBulkMenu() {
	var selectedCount = $('form#product_catalog_list input:checked[name="bulk_action_selected_products[]"][disabled!="disabled"]').size();
	$('#product_bulk_menu').prop('disabled', (selectedCount === 0));
}

var productCatalogFilterChanged = false;
function updateFilterMenu() {
	var count = $('form#product_catalog_list tr.column-filters select option:selected[value!=""]').size();
	$('form#product_catalog_list tr.column-filters input[type="text"]:visible').each(function() {
		if ($(this).val() !== '') {
			count ++;
		}
	});
	$('form#product_catalog_list tr.column-filters input[type="text"][sql!=""][sql]').each(function() {
		if ($(this).val() !== '') {
			count ++;
		}
	});
	$('button[name="products_filter_submit"]').prop('disabled', (count === 0) && productCatalogFilterChanged === false);
	if (count === 0 && productCatalogFilterChanged === false) {
		$('button[name="products_filter_reset"]').hide();
	}else {
		$('button[name="products_filter_reset"]').show();
	}
}

function productCategoryFilterReset(div) {
	$('div#choice_tree').categorytree('unselect');
	$('form#product_catalog_list input[name="filter_category"]').val('');
	$('form#product_catalog_list').submit();
}

function productCategoryFilterExpand(div, btn) {
	$('div#choice_tree').categorytree('unfold');
}

function productCategoryFilterCollapse(div, btn) {
	$('div#choice_tree', div).categorytree('fold');
}

function categoryFilterButtons() {
	if ($('div#product_catalog_category_tree_filter ul ul:visible').size() === 0) {
		$('div#product_catalog_category_tree_filter ~ div button[name="product_catalog_category_tree_filter_collapse"]').hide();
	} else {
		$('div#product_catalog_category_tree_filter ~ div button[name="product_catalog_category_tree_filter_collapse"]').show();
	}
	if ($('div#product_catalog_category_tree_filter ul ul:hidden').size() === 0) {
		$('div#product_catalog_category_tree_filter ~ div button[name="product_catalog_category_tree_filter_expand"]').hide();
	} else {
		$('div#product_catalog_category_tree_filter ~ div button[name="product_catalog_category_tree_filter_expand"]').show();
	}
	if ($('div#product_catalog_category_tree_filter ul input:checked').size() === 0) {
		$('div#product_catalog_category_tree_filter ~ div button[name="product_catalog_category_tree_filter_reset"]').hide();
	} else {
		$('div#product_catalog_category_tree_filter ~ div button[name="product_catalog_category_tree_filter_reset"]').show();
	}
}

function productColumnFilterReset(tr) {
	$('input:text', tr).val('');
	$('select option:selected', tr).prop('selected', false);
	$('input#filter_column_price', tr).attr('sql', '');
	$('input#filter_column_sav_quantity', tr).attr('sql', '');
	$('input#filter_column_id_product', tr).attr('sql', '');
	$('form#product_catalog_list').submit();
}

function bulkModalAction(allItems, postUrl, redirectUrl, action) {
  var itemsCount = allItems.length;
  var currentItemIdx = 0;
  if (itemsCount < 1) {
    return;
  }

  var targetModal = $('#catalog_' + action + '_modal');
  targetModal.modal('show');

  var details = targetModal.find('#catalog_' + action + '_progression .progress-details-text');
  var progressBar = targetModal.find('#catalog_' + action + '_progression .progress-bar');
  var failure = targetModal.find('#catalog_' + action + '_failure');

  // re-init popup
  details.html(details.attr('default-value'));

  progressBar.css('width', '0%');
  progressBar.find('span').html('');
  progressBar.removeClass('progress-bar-danger');
  progressBar.addClass('progress-bar-success');

  failure.hide();

  // call in ajax. Recursive with inner function
  var bulkCall = function (items, successCallback, errorCallback) {
    if (items.length === 0) {
      return;
    }
    var item0 = $(items.shift()).val();
    currentItemIdx++;

    details.html(details.attr('default-value').replace(/\.\.\./, '') + ' (#' + item0 + ')');
    $.ajax({
      type: 'POST',
      url: postUrl,
      data: {bulk_action_selected_products: [item0]},
      success: function (data, status) {
        progressBar.css('width', (currentItemIdx * 100 / itemsCount) + '%');
        progressBar.find('span').html(currentItemIdx + ' / ' + itemsCount);

        if (items.length > 0) {
          bulkCall(items, successCallback, errorCallback);
        } else {
          successCallback();
        }
      },
      error: errorCallback,
      dataType: 'json'
    });
  };

  bulkCall(allItems.toArray(), function () {
    window.location.href = redirectUrl;
  }, function () {
    progressBar.removeClass('progress-bar-success');
    progressBar.addClass('progress-bar-danger');
    failure.show();
    window.location.href = redirectUrl;
  });
}

function bulkProductAction(element, action) {
  var form = $('form#product_catalog_list');
  var postUrl = '';
  var redirectUrl = '';
  var urlHandler = null;

  var items = $('input:checked[name="bulk_action_selected_products[]"]', form);
  if (items.size() === 0) {
    return false;
  } else {
    urlHandler = $(element).closest('[bulkurl]');
  }

  switch (action) {
    case 'delete_all':
      postUrl = urlHandler.attr('bulkurl').replace(/activate_all/, action);
      redirectUrl = urlHandler.attr('redirecturl');

      // Confirmation popup and callback...
      $('#catalog_deletion_modal').modal('show');
      $('#catalog_deletion_modal button[value="confirm"]').off('click');
      $('#catalog_deletion_modal button[value="confirm"]').on('click', function () {

        $('#catalog_deletion_modal').modal('hide');

        return bulkModalAction(items, postUrl, redirectUrl, action);
      });

      return; // No break, but RETURN, to avoid code after switch block :)

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
    case 'edition':
      var editionAction;
      var bulkEditionSelector = '#bulk_edition_toolbar input:submit';
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
    var redirectionInput = $('<input>')
      .attr('type', 'hidden')
      .attr('name', 'redirect_url').val(redirectUrl);
    form.append($(redirectionInput));
    form.attr('action', postUrl);
    form.submit();
  }
  return false;
}

function unitProductAction(element, action) {
  var form = $('form#product_catalog_list');

  // save action URL for redirection and update to post to bulk action instead
  // using form action URL allow to get route attributes and stay on the same page & ordering.
  var urlHandler = $(element).closest('[data-uniturl]');
  var redirectUrlHandler = $(element).closest('[redirecturl]');
  var redirectionInput = $('<input>')
    .attr('type', 'hidden')
    .attr('name', 'redirect_url').val(redirectUrlHandler.attr('redirecturl'));

  switch (action) {
    case 'delete':
      // Confirmation popup and callback...
      $('#catalog_deletion_modal').modal('show');
      $('#catalog_deletion_modal button[value="confirm"]').off('click');
      $('#catalog_deletion_modal button[value="confirm"]').on('click', function () {
        form.append($(redirectionInput));
        var url = urlHandler.attr('data-uniturl').replace(/duplicate/, action);
        form.attr('action', url);
        form.submit();

        $('#catalog_deletion_modal').modal('hide');
      });
      return;
    // Other cases, nothing to do, continue.
    //default:
  }

  form.append($(redirectionInput));
  var url = urlHandler.attr('data-uniturl').replace(/duplicate/, action);
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
	var form = $('form#product_catalog_list');

	switch (action) {
		/*
		case 'quantity_edition':
			showBulkProductEdition(true);
			$('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form).prop('disabled', true);

			i = 1;
			$('td.product-sav-quantity', form).each(function() {
				$quantity = $(this).attr('productquantityvalue');
				$product_id = $(this).closest('tr[productid]').attr('productid');
				$input = $('<input>').attr('type', 'text').attr('name', 'bulk_action_edit_quantity['+$product_id+']')
					.attr('tabindex', i++)
					.attr('onkeydown', 'if (event.keyCode == 13) return bulkProductAction(this, "edition_next"); if (event.keyCode == 27) return bulkProductEdition(this, "cancel");')
					.val($quantity);
				$(this).html($input);

			});
			$('#bulk_edition_toolbar input:submit').attr('tabindex', i++);
			$('#bulk_edition_toolbar input:button').attr('tabindex', i++);
			$('#bulk_edition_toolbar input:submit').attr('editionaction', action);

			$('td.product-sav-quantity input', form).first().focus();
			break;
		*/
		case 'sort':
			showBulkProductEdition(true);
			$('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form).prop('disabled', true);
			$('#bulk_edition_toolbar input:submit').attr('editionaction', action);
			break;
		case 'cancel':
			// quantity inputs
			$('td.product-sav-quantity', form).each(function() {
				$(this).html($(this).attr('productquantityvalue'));
			});

			$('#bulk_edition_toolbar input:submit').removeAttr('editionaction');
			showBulkProductEdition(false);
			$('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form).prop('disabled', false);
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

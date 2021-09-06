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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var is_installing = false;
$(document).ready(function() {
  $("#loaderSpace").unbind('ajaxStart');
  start_install();
});

current_step = 0;
function start_install() {
  // If we are already installing PrestaShop, do not trigger action again
  if (is_installing) {
    return;
  }

  is_installing = true;

  $('.process_step').removeClass('fail').removeClass('success').slideUp().hide();
  $('.error_log').hide();
  $('#progress_bar').show();
  $('#progress_bar .installing').show();
  $('.stepList li:last-child').removeClass('ok').removeClass('ko');
  process_pixel = parseInt($('#progress_bar .total').css('width')) / process_steps.length;
  $('#tabs li a').each(function() {
    this.rel = $(this).attr('href');
    this.href = '#';
  });

  process_install();
}

function process_install(step) {
  if (!step) {
    step = process_steps[0];
  }

  $('#process_step_'+step.key).slideDown();
  $('.installing').hide().html(step.lang + '...').fadeIn('slow');

  $.ajax({
    url: 'index.php',
    data: step.key + '=true',
    dataType: 'json',
    cache: false,
    success: function(json) {
      // No error during this step
      if (json && json.success === true) {
        $('#process_step_'+step.key).addClass('success');
        current_step++;
        if (current_step >= process_steps.length) {
          $('#progress_bar .total .progress').animate({'width': '100%'}, 500);
          $('#progress_bar .total span').html('100%');

          // Installation finished
          setTimeout(function(){install_success();}, 700);
        } else {
          $('#progress_bar .total .progress').animate({'width': '+='+process_pixel+'px'}, 500);
          $('#progress_bar .total span').html(Math.ceil(current_step * (100 / process_steps.length))+'%');

          // Process next step
          if (process_steps[current_step].subtasks) {
            process_install_subtasks(process_steps[current_step]);
          } else {
            process_install(process_steps[current_step]);
          }
        }
      } else {
        install_error(step, (json) ? json.message : '');
      }
    },
    // An error HTTP (page not found, json not valid, etc.) occurred during this step
    error: function( jqXHR, textStatus ) {
      var errorMsg = 'HTTP '+ jqXHR.status + ' - '+ textStatus +' - '+ jqXHR.responseText;
      install_error(step, errorMsg);
    }
  });
}

function process_install_subtasks(step) {
  $('.installing').hide().html(step.lang+'...').fadeIn('slow');
  process_install_subtask(step, 0);
}

function process_install_subtask(step, current_subtask) {
  var params = {};
  params[step.key] = 'true';
  params['subtask'] = current_subtask;
  $.each(step.subtasks[current_subtask], function(k, v) {
    params[k] = v;
  });

  $.ajax({
    url: 'index.php',
    data: params,
    dataType: 'json',
    cache: false,
    success: function(json) {
      // No error during this step
      if (json && json.success === true) {
        current_subtask++;
        var subtask_process_pixel = process_pixel / step.subtasks.length;
        $('#progress_bar .total .progress').animate({'width': '+='+subtask_process_pixel+'px'}, 500);
        $('#progress_bar .total span').html(Math.ceil((current_step * (100 / process_steps.length)) + Math.ceil(current_subtask * ((100 / process_steps.length) / step.subtasks.length)))+'%');

        if (current_subtask >= step.subtasks.length) {
          current_step++;
          $('#process_step_'+step.key).show().addClass('success');
          if (process_steps[current_step].subtasks) {
            process_install_subtasks(process_steps[current_step]);
          } else {
            process_install(process_steps[current_step]);
          }
        } else {
          process_install_subtask(step, current_subtask);
        }
      } else {
        install_error(step, (json) ? json.message : '');
      }
    },
    // An error HTTP (page not found, json not valid, etc.) occurred during this step
    error: function( jqXHR, textStatus ) {
      var errorMsg = 'HTTP '+ jqXHR.status + ' - '+ textStatus +' - '+ jqXHR.responseText;
      install_error(step, errorMsg);
    }
  });
}

function install_error(step, errors) {
  current_step = 0;
  is_installing = false;

  $('#error_process').show();
  $('#process_step_'+step.key).show().addClass('fail');
  $('#progress_bar .total .progress').stop();
  $('#progress_bar .installing').slideUp();
  $('.stepList li:last-child').addClass('ko');

  if (errors) {
    var list_errors = errors;

    if ($.type(list_errors) == 'string') {
      list_errors = [];
      list_errors[0] = errors;
    } else if ($.type(list_errors) == 'array') {
      list_errors = [list_errors[0]];
    }

    var display = '<ol>';
    $.each(list_errors, function(k, v) {
      display += '<li>' + (k + 1) + ': ' + v.replace(/\n/g, '<br>') + '</li>';
    });

    display += '</ol>';
    $('#error_process').append(display);
  }

  $('#tabs li a').each(function() {
    this.href=this.rel;
  });
}

function install_success() {
  $('#progress_bar .total span').hide();
  $('.installing').html(install_is_done);

  is_installing = false;
  $('#install_process_form').slideUp();
  $('#install_process_success').slideDown();
  $('.stepList li:last-child').addClass('ok');

  $('#tabs li a').each(function () {
    this.href = this.rel;
  });
}

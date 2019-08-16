/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
$(document).ready( function () {
  gamificationTasks();
});

function gamificationTasks()
{
  $('#gamification_notif').remove();
  $('#notifs_icon_wrapper').append('<div id="gamification_notif" class="notifs"></div>');
  $.ajax({
    type: 'POST',
    url: admin_gamification_ajax_url,
    dataType: 'json',
    data: {
      controller : 'AdminGamification',
      action : 'gamificationTasks',
      ajax : true,
      id_tab : current_id_tab,
      ids_ps_advice : ids_ps_advice,
    },
    success: function(jsonData)
    {
      for (var i in jsonData.advices_to_display.advices)
      {
        ok = false;
        selector = jsonData.advices_to_display.advices[i].selector.split(',');
        for (var j in selector)
        {
          if (!ok)
          {
            if (jsonData.advices_to_display.advices[i].location == 'after')
              $(selector[j]).after(jsonData.advices_to_display.advices[i].html);
            else
              $(selector[j]).before(jsonData.advices_to_display.advices[i].html);

            if ($(selector[j]).length)
              ok = true;
          }
        }
      }

      initHeaderNotification(jsonData.header_notification);

      var fancybox = $('.gamification_fancybox');
      if (fancybox.fancybox) {
        fancybox.fancybox();
      }
    }
  });
}

function initHeaderNotification(html)
{
  $('#gamification_notif').remove();
  $('#notifs_icon_wrapper').append(html);
  $('#gamification_notif').click(function () {
    if ($('#gamification_notif_wrapper').css('display') == 'block')
    {
      $('#gamification_notif_wrapper').hide();
    }
    else
    {
      disabledGamificationNotification();
      $('.notifs_wrapper').hide();
      $('#gamification_notif_number_wrapper').hide();
      $('#gamification_notif_wrapper').show();
      if (typeof(admintab_gamification) != "undefined")
      {
        $('#gamification_progressbar').progressbar({
          change: function() {
                if (current_level_percent)
                  $( ".gamification_progress-label" ).html( gamification_level+' '+current_level+' : '+$('#gamification_progressbar').progressbar( "value" ) + "%" );
                else
                  $( ".gamification_progress-label" ).html('');
              },
          });
        $('#gamification_progressbar').progressbar("value", current_level_percent );
      }
    }
  });
  if ($('.dropdown-toggle').length)
    $('.dropdown-toggle').dropdown();
}

function disabledGamificationNotification()
{
  $.ajax({
    type: 'POST',
    url: admin_gamification_ajax_url,
    data: {
      controller : 'AdminGamification',
      action : 'disableNotification',
      ajax : true
    },
    success: function(jsonData)
    {
      $('#gamification_notif_number_wrapper').hide();
    }
  });
}

function initBubbleDescription()
{
  $('.badge_square').each( function () {
    if ($(this).children('.gamification_badges_description').text().length)
    {
      $(this).CreateBubblePopup({
        position : 'top',
        openingDelay:0,
        alwaysVisible: false,
        align	 : 'center',
        innerHtml: $(this).children('.gamification_badges_description').text(),
        innerHtmlStyle: { color:'#000',  'text-align':'center' },
        themeName: 'black',
        themePath: '../modules/gamification/views/jquerybubblepopup-themes'
      });
    }
  });
}


function filterBadge(type)
{
  group = '.'+$('#group_select_'+type+' option:selected').val();
  status = '.'+$('#status_select_'+type+' option:selected').val();
  level = '.'+$('#level_select_'+type+' option:selected').val();

  if (group == '.undefined')
    group = '';
  if (status == '.undefined')
    status = '';
  if (level == '.undefined')
    level = '';

  $('#list_'+type).isotope({filter: '.badge_square'+group+status+level, animationEngine : 'css'});

  if (!$('#list_'+type+' li').not('.isotope-hidden').length)
    $('#no_badge_'+type).fadeIn();
  else
    $('#no_badge_'+type).fadeOut();
}




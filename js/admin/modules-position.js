/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(function(){

	var panelSelection = $("#modules-position-selection-panel");
	var panelSelectionSingleSelection = panelSelection.find("#modules-position-single-selection");
	var panelSelectionMultipleSelection = panelSelection.find("#modules-position-multiple-selection");

	var panelSelectionOriginalY = panelSelection.offset().top;

	var pageHeader = $(".page-head");
	var panelSelectionOriginalYTopMargin = 111;

	panelSelection.css("position", "relative").hide();

	$(window).on("scroll", function(event) {
		var scrollTop = $(window).scrollTop();
		panelSelection.css(
			"top",
			scrollTop < panelSelectionOriginalYTopMargin
				? 0
				: scrollTop - panelSelectionOriginalY + panelSelectionOriginalYTopMargin
		);
	});

	var modulesList = $(".modules-position-checkbox");

	modulesList.on("change", function(){
		var checkedCount = modulesList.filter(":checked").length;
		panelSelection.hide();
		panelSelectionSingleSelection.hide();
		panelSelectionMultipleSelection.hide();
		if (checkedCount == 1) {

			panelSelection.show();
			panelSelectionSingleSelection.show();
		} else if (checkedCount > 1) {
			panelSelection.show();
			panelSelectionMultipleSelection.show();
			panelSelectionMultipleSelection.find("#modules-position-selection-count").html(checkedCount);
		}
	});

	panelSelection.find("button").click(function(){
		$("button[name='unhookform']").trigger("click");
	});

	var hooksList = [];
	$("section.hook_panel").find(".hook_name").each(function(){
		var $this = $(this);
		hooksList.push({
			'title' : $this.html(),
			'element' : $this,
			'container' : $this.parents(".hook_panel")
		});
	});

	var showModules = $("#show_modules");
	showModules.select2();
	showModules.bind("change", function() {
		modulesPositionFilterHooks();
	});

	$('#hook_search').bind('input', function() {
		modulesPositionFilterHooks();
	});

	function modulesPositionFilterHooks() {

		var id = 0;
		var hookName = $('#hook_search').val();
		var moduleId = $("#show_modules").val();
		var regex = new RegExp("("+hookName+")", "gi");

		for (id = 0; id < hooksList.length; id++) {
			hooksList[id].container.toggle(hookName == "" && moduleId == "all");
			hooksList[id].element.html(hooksList[id].title);
			hooksList[id].container.find('.module_list_item').removeClass('highlight' );
		}


		if (hookName != "" || moduleId != "all") {

			var hooksToShowFromModule = $();
			var hooksToShowFromHookName = $();

			if (moduleId != "all") {
				for (id = 0; id < hooksList.length; id++) {
					var currentHooks = hooksList[id].container.find(".module_position_"+moduleId);
					if (currentHooks.length > 0) {
						hooksToShowFromModule = hooksToShowFromModule.add(hooksList[id].container);
						currentHooks.addClass('highlight');
					}
				}
			}

			if (hookName != "") {
				for (id = 0; id < hooksList.length; id++) {
					var start = hooksList[id].title.toLowerCase().search(hookName.toLowerCase());
					if (start != -1) {
						hooksToShowFromHookName = hooksToShowFromHookName.add(hooksList[id].container);
						hooksList[id].element.html(hooksList[id].title.replace(regex, '<span class="highlight">$1</span>'));
					}
				}
			}

			if (moduleId == "all" && hookName != "") {
				hooksToShowFromHookName.show();
			} else if (hookName == "" && moduleId != "all") {
				hooksToShowFromModule.show();
			} else {
				hooksToShowFromHookName.filter(hooksToShowFromModule).show();
			}
		}
	}
});
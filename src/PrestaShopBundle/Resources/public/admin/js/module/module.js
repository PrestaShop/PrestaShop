$(document).ready(function() {

  console.log('Page Module inited');
  var controller = new AdminModule();
  controller.init();

});

/**
 * AdminModule Page Controller.
 * @constructor
 */
var AdminModule = function() {

/**
 * Initialize all listners and bind everything
 * @method init
 * @memberof AdminModule
 */
  this.init = function() {
    this.initSortingDisplaySwitch();
  };

  /**
   * Initialize display switching between List or Grid
   * @method initSortingDisplaySwitch
   * @memberof AdminModule
   */
  this.initSortingDisplaySwitch = function() {
    var _this = this;

    $('.module-sort-switch').on('click', function() {
      var switchTo = $(this).attr('data-switch');
      var isAlreadyDisplayed = $(this).hasClass('active-display');
      console.log(switchTo, isAlreadyDisplayed);
      if (typeof switchTo != 'undefined' && isAlreadyDisplayed === false) {
        _this.switchSortingDisplayTo(switchTo);
      }
    });
  };

  /**
   * Initialize display switching between List or Grid
   * @method switchSortingDisplayTo
   * @memberof AdminModule
   * @param {string} switchTo name of the display to switch to
   * @return {boolean}
   */
  this.switchSortingDisplayTo = function(switchTo) {
      var _this = this;

      if (switchTo == 'grid') {
          console.log('Change for GRID display');
          $('.module-item-list').each(function() {
              $('#module-sort-list').removeClass('module-sort-active');
              $('#module-sort-grid').addClass('module-sort-active');
              $(this).removeClass();
              $(this).addClass('module-item-grid col-md-3');
              _this.setNewDisplay($(this), '-list', '-grid');
          });
      } else if (switchTo == 'list') {
          console.log('Change for LIST display');
          $('.module-item-grid').each(function(index) {
              $('#module-sort-grid').removeClass('module-sort-active');
              $('#module-sort-list').addClass('module-sort-active');
              $(this).removeClass();
              $(this).addClass('module-item-list col-md-12');
              // Add grey background for LIST
              var needGreyBackground = index % 2;
              if (needGreyBackground) {
                  $(this).addClass('module-item-list-grey');
              }
              _this.setNewDisplay($(this), '-grid', '-list');
          });
      } else {
          console.error('Can\'t switch to undefined display property "'+switchTo+'"');
          return false;
      }

      return true;
  };

  /**
   * Initialize display switching between List or Grid
   * @method switchSortingDisplayTo
   * @memberof AdminModule
   * @param {string} domObj jQuery Dom Element
   * @param {string} toBeReplaced the string that has to be replaced
   * @param {string} replaceWith the string to replace toBeReplaced with
   */
  this.setNewDisplay = function(domObj, toBeReplaced, replaceWith) {
      var replaceRegex = new RegExp(toBeReplaced, 'g');
      var originalHTML = domObj.html();
      var alteredHTML = originalHTML.replace(replaceRegex, replaceWith);
      domObj.empty().html(alteredHTML);
  };



};

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * By default, bootstrap dropdowns close down when the user clicks anywhere.
 * This plugin allows clicking inside the dropdown menu while keeping it open.
 * In order to make a dropdown behave like this, simply add the class "dropdown-clickable" to its parent element.
 */
(($) => {
  // eslint-disable-next-line
  $.fn.clickableDropdown = function clickableDropdown() {
    $(document).on('click', '.dropdown-clickable .dropdown-menu', (e) => {
      e.stopPropagation();
    });

    return this;
  };

  // hook up the plugin
  $(() => {
    $(document).clickableDropdown();
  });
})(window.$);

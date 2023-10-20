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

const {$} = window;

/**
 * Handles UI interactions of choice tree
 */
export default class ChoiceTree {
  $container: JQuery<HTMLElement>;

  /**
   * @param {String} treeSelector
   */
  constructor(treeSelector: string) {
    this.$container = $(treeSelector);

    this.$container.on('click', '.js-input-wrapper', (event) => {
      const $inputWrapper = $(event.currentTarget);

      this.toggleChildTree($inputWrapper);
    });

    this.$container.on('click', '.js-toggle-choice-tree-action', (event) => {
      const $action = $(event.currentTarget);

      this.toggleTree($action);
    });
  }

  /**
   * Enable automatic check/uncheck of clicked item's children.
   */
  enableAutoCheckChildren(): void {
    this.$container.on('change', 'input[type="checkbox"]', (event) => {
      const $clickedCheckbox = $(event.currentTarget);
      const $itemWithChildren = $clickedCheckbox.closest('li');

      $itemWithChildren
        .find('ul input[type="checkbox"]')
        .prop('checked', $clickedCheckbox.is(':checked'));
    });
  }

  /**
   * Enable all inputs in the choice tree.
   */
  enableAllInputs(): void {
    this.$container.find('input').removeAttr('disabled');
  }

  /**
   * Disable all inputs in the choice tree.
   */
  disableAllInputs(): void {
    this.$container.find('input').attr('disabled', 'disabled');
  }

  /**
   * Collapse or expand sub-tree for single parent
   *
   * @param {jQuery} $inputWrapper
   *
   * @private
   */
  toggleChildTree($inputWrapper: JQuery<HTMLElement>): void {
    const $parentWrapper = $inputWrapper.closest('li');

    if ($parentWrapper.hasClass('expanded')) {
      $parentWrapper.removeClass('expanded').addClass('collapsed');

      return;
    }

    if ($parentWrapper.hasClass('collapsed')) {
      $parentWrapper.removeClass('collapsed').addClass('expanded');
    }
  }

  /**
   * Collapse or expand whole tree
   *
   * @param {jQuery} $action
   *
   * @private
   */
  private toggleTree($action: JQuery<HTMLElement>): void {
    const $parentContainer = $action.closest('.js-choice-tree-container');
    const action: string = $action.data('action');

    // toggle action configuration
    const config: Record<string, Record<string, string>> = {
      addClass: {
        expand: 'expanded',
        collapse: 'collapsed',
      },
      removeClass: {
        expand: 'collapsed',
        collapse: 'expanded',
      },
      nextAction: {
        expand: 'collapse',
        collapse: 'expand',
      },
      text: {
        expand: 'collapsed-text',
        collapse: 'expanded-text',
      },
      icon: {
        expand: 'collapsed-icon',
        collapse: 'expanded-icon',
      },
    };

    $parentContainer.find('li').each((index, item) => {
      const $item = $(item);

      if ($item.hasClass(config.removeClass[action])) {
        $item
          .removeClass(config.removeClass[action])
          .addClass(config.addClass[action]);
      }
    });

    $action.data('action', config.nextAction[action]);
    $action.find('.material-icons').text($action.data(config.icon[action]));
    $action.find('.js-toggle-text').text($action.data(config.text[action]));
  }
}

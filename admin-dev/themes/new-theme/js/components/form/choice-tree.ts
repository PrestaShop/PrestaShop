/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/** Marks the container of one tree, as rendered by the material choice tree form theme. */
export const CHOICE_TREE_CONTAINER_SELECTOR = '.js-choice-tree-container';

/** Key under which a tree keeps its instance on its own container. */
export const CHOICE_TREE_DATA_KEY = 'choiceTree';

/**
 * Handles UI interactions of choice tree
 */
export default class ChoiceTree {
  $container: JQuery<HTMLElement>;

  /**
   * @param {String|JQuery} tree selector for, or container of, a single tree
   */
  constructor(tree: string | JQuery<HTMLElement>) {
    // WHY: this class is exposed through window.prestashop.component, where initComponents() builds
    // every component with no argument. Built that way it used to bind its handlers to $(undefined),
    // an empty selection, and do nothing at all without reporting anything - so point the caller at
    // the component that does support that contract. An empty selection is still accepted, because
    // several pages build a tree unconditionally on a form that does not always render one.
    if (!tree) {
      throw new Error(
        'ChoiceTree must be constructed with the selector or the container of a single tree. '
          + 'To initialize every choice tree of a page at once, use the ChoiceTreeWidget component instead.',
      );
    }

    this.$container = typeof tree === 'string' ? $(tree) : tree;

    // The instance is reachable through the container's data, which is how ChoiceTreeWidget can
    // tell a tree a page has already built from one that still needs building.
    this.$container.data(CHOICE_TREE_DATA_KEY, this);

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
    const $parentContainer = $action.closest(CHOICE_TREE_CONTAINER_SELECTOR);
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

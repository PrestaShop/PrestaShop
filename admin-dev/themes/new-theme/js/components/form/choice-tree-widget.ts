/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ChoiceTree, {
  CHOICE_TREE_CONTAINER_SELECTOR,
  CHOICE_TREE_DATA_KEY,
} from '@js/components/form/choice-tree';

const {$} = window;

/**
 * Initializes every choice tree rendered on the page.
 *
 * ChoiceTree itself handles one tree and needs to be told which, which is why the pages that own a
 * tree build it themselves. A form that only needs the expanding and collapsing behaviour has
 * nothing to build, and no way to ask for it: initComponents() constructs every component with no
 * argument, so ChoiceTree was given no container and quietly bound its handlers to an empty
 * selection. This component is the missing step, so adding a CategoryChoiceTreeType - or any other
 * choice tree - to a form works with:
 *
 *   window.prestashop.component.initComponents(['ChoiceTreeWidget']);
 *
 * It deliberately builds a plain ChoiceTree. Automatic checking of children is a separate opt-in
 * that only the form knows it wants, so a page needing it still builds its own tree and calls
 * enableAutoCheckChildren() on it.
 */
export default class ChoiceTreeWidget {
  private readonly containerSelector: string;

  constructor(options: Record<string, any> = {}) {
    const opts = options || {};

    this.containerSelector = opts.containerSelector || CHOICE_TREE_CONTAINER_SELECTOR;

    this.init();
  }

  /**
   * Returns the tree bound to a container, whether this component or the page built it.
   *
   * @param {JQuery} $container container of a single tree
   */
  // eslint-disable-next-line class-methods-use-this
  getChoiceTree($container: JQuery): ChoiceTree | undefined {
    return $container.data(CHOICE_TREE_DATA_KEY);
  }

  /**
   * @private
   */
  private init(): void {
    $(this.containerSelector).each((index: number, container: HTMLElement): void => {
      const $container = $(container);

      // WHY: a page that builds its own tree keeps ownership of it. Building a second one over the
      // same container would bind the toggle handlers twice, so every click would expand and then
      // immediately collapse the same branch.
      if ($container.data(CHOICE_TREE_DATA_KEY)) {
        return;
      }

      new ChoiceTree($container);
    });
  }
}

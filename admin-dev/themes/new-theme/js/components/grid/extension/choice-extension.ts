/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * This extension enables submit functionality of the choice fields in grid.
 *
 * Usage of the extension:
 *
 * const myGrid = new Grid('myGrid');
 * myGrid.addExtension(new ChoiceExtension());
 *
 */
export default class ChoiceExtension {
  lockArray: Array<string>;

  constructor() {
    this.lockArray = [];
  }

  extend(grid: Grid): void {
    const $choiceOptionsContainer = grid
      .getContainer()
      .find(GridMap.bulks.choiceOptions);

    this.letOpenMenusEscapeTheScrollingTable(grid);

    $choiceOptionsContainer.find(GridMap.dropdownItem).on('click', (e) => {
      e.preventDefault();
      const $button = $(e.currentTarget);
      const $parent = $button.closest(GridMap.bulks.choiceOptions);
      const url = $parent.data('url');

      this.submitForm(url, $button);
    });
  }

  /**
   * The grid sits inside a `.table-responsive`, which sets `overflow-x: auto`. Once either axis is
   * not `visible` the other one clips too, so a status menu taller than the table is cut off - which
   * is what happens on a short list, where the table is only a few rows high.
   *
   * The horizontal scrolling is still wanted for wide grids, so rather than dropping it the
   * container is allowed to overflow only while a menu is open.
   *
   * @param {Grid} grid
   * @private
   */
  private letOpenMenusEscapeTheScrollingTable(grid: Grid): void {
    const $container = grid.getContainer();

    $container.on('show.bs.dropdown', (e: JQuery.TriggeredEvent) => {
      if ($(e.target).find(GridMap.bulks.choiceOptions).length === 0) {
        return;
      }

      $(e.target).closest('.table-responsive').css('overflow', 'visible');
    });

    $container.on('hide.bs.dropdown', (e: JQuery.TriggeredEvent) => {
      if ($(e.target).find(GridMap.bulks.choiceOptions).length === 0) {
        return;
      }

      $(e.target).closest('.table-responsive').css('overflow', '');
    });
  }

  /**
   * Submits the form.
   * @param {string} url
   * @param {jQuery} $button
   * @private
   */
  private submitForm(url: string, $button: JQuery) {
    const selectedStatusId = $button.data('value');

    if (this.isLocked(url)) {
      return;
    }

    const $form = $('<form>', {
      action: url,
      method: 'POST',
    }).append(
      $('<input>', {
        name: 'value',
        value: selectedStatusId,
        type: 'hidden',
      }),
    );

    $form.appendTo('body');
    $form.submit();

    this.lock(url);
  }

  /**
   * Checks if current url is being used at the moment.
   *
   * @param url
   * @return {boolean}
   *
   * @private
   */
  private isLocked(url: string): boolean {
    return this.lockArray.includes(url);
  }

  /**
   * Locks the current url so it cant be used twice to execute same request
   * @param url
   * @private
   */
  private lock(url: string): void {
    this.lockArray.push(url);
  }
}

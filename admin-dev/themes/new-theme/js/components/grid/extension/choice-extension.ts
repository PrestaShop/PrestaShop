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

    $choiceOptionsContainer.find(GridMap.dropdownItem).on('click', (e) => {
      e.preventDefault();
      const $button = $(e.currentTarget);
      const $parent = $button.closest(GridMap.bulks.choiceOptions);
      const url = $parent.data('url');

      this.submitForm(url, $button);
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

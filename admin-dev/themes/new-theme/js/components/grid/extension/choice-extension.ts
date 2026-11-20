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
    this.enableChoiceSearch(grid);

    $choiceOptionsContainer.find(GridMap.dropdownItem).on('click', (e) => {
      e.preventDefault();
      const $button = $(e.currentTarget);
      const $parent = $button.closest(GridMap.bulks.choiceOptions);
      const url = $parent.data('url');

      this.submitForm(url, $button);
    });
  }

  /**
   * Filters the choices of a searchable column as the user types.
   * Enter applies the first match; it is intercepted because the grid sits inside the filters form.
   *
   * @param {Grid} grid
   * @private
   */
  private enableChoiceSearch(grid: Grid): void {
    const $container = grid.getContainer();

    $container.on('shown.bs.dropdown', (e: JQuery.TriggeredEvent) => {
      const $search = $(e.target).find(GridMap.choiceSearch);

      if ($search.length === 0) {
        return;
      }

      $search.val('');
      this.filterChoices($search);
      $search.trigger('focus');
    });

    $container.on('input', GridMap.choiceSearch, (e: JQuery.TriggeredEvent) => {
      this.filterChoices($(e.currentTarget));
    });

    $container.on('keydown', GridMap.choiceSearch, (e: JQuery.KeyDownEvent) => {
      if (e.key !== 'Enter' && e.key !== 'ArrowDown') {
        return;
      }

      e.preventDefault();
      const $firstMatch = $(e.currentTarget)
        .closest(GridMap.bulks.choiceOptions)
        .find(`${GridMap.dropdownItem}:not(.d-none)`)
        .first();

      // Bootstrap ignores arrow keys typed in an input of the menu, so the first move is done here
      $firstMatch.trigger(e.key === 'Enter' ? 'click' : 'focus');
    });
  }

  /**
   * @param {JQuery} $search
   * @private
   */
  private filterChoices($search: JQuery): void {
    const term = ChoiceExtension.normalize(String($search.val()));
    const $menu = $search.closest(GridMap.bulks.choiceOptions);
    let matchCount = 0;

    $menu.find(GridMap.dropdownItem).each((index: number, item: HTMLElement) => {
      const matches = ChoiceExtension.normalize($(item).text()).includes(term);
      $(item).toggleClass('d-none', !matches);

      if (matches) {
        matchCount += 1;
      }
    });

    $menu.find(GridMap.choiceNoResults).toggleClass('d-none', matchCount > 0);
  }

  /**
   * Makes the search case and accent insensitive ("accepte" finds "Paiement accepté").
   *
   * @param {string} value
   * @private
   */
  private static normalize(value: string): string {
    return value
      .normalize('NFD')
      .replace(/[̀-ͯ]/g, '')
      .toLowerCase()
      .trim();
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

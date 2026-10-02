/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ComponentsMap from '@components/components-map';
import EntitySearchInput, {ENTITY_SEARCH_INPUT_DATA_KEY} from '@components/entity-search-input';

/**
 * Initializes every widget rendered by EntitySearchInputType on the page.
 *
 * EntitySearchInput itself handles one widget and needs its container, which is why pages that
 * customize a widget build it themselves. Forms that only need the default behaviour have nothing
 * to build: the twig template already exposes the whole configuration as data attributes, and
 * EntitySearchInput reads them with the priority "option > data attribute > default". This
 * component is the missing step that turns those attributes into live widgets, so adding an
 * EntitySearchInputType to a form works with:
 *
 *   window.prestashop.component.initComponents(['EntitySearchWidget']);
 */
export default class EntitySearchWidget {
  private readonly widgetSelector: string;

  constructor(options: Record<string, any> = {}) {
    const opts = options || {};

    this.widgetSelector = opts.widgetSelector || ComponentsMap.entitySearchInput.widgetSelector;

    this.init(opts.entitySearchInputOptions || {});
  }

  /**
   * Returns the widget bound to a container, whether this component or the page built it. Useful to
   * attach callbacks to an automatically initialized widget.
   *
   * @param {JQuery} $container container of a single widget
   */
  // eslint-disable-next-line class-methods-use-this
  getEntitySearchInput($container: JQuery): EntitySearchInput | undefined {
    return $container.data(ENTITY_SEARCH_INPUT_DATA_KEY);
  }

  /**
   * @param {Object} entitySearchInputOptions options forwarded to each widget
   *
   * @private
   */
  private init(entitySearchInputOptions: Record<string, any>): void {
    $(this.widgetSelector).each((index: number, widget: HTMLElement): void => {
      const $widget = $(widget);

      // WHY: a page that builds a widget itself, to pass callbacks that no generic initialization
      // can provide, must keep ownership of it. Initializing it twice would bind two typeaheads to
      // the same input and add every selected entity to both.
      if ($widget.data(ENTITY_SEARCH_INPUT_DATA_KEY)) {
        return;
      }

      // The instance is reachable through the container's data, which getEntitySearchInput reads.
      new EntitySearchInput($widget, entitySearchInputOptions);
    });
  }
}

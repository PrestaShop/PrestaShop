/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ComponentsMap from '@components/components-map';
import EntitySearchInput from '@js/components/entity-search-input';

const {$} = window;

/**
 * Initialises every entity search input that asked for it in its form type, so a page does not have
 * to instantiate them one by one.
 *
 * EntitySearchInput itself stays a one container component: it builds a remote source and an
 * autocomplete widget per container, so the loop belongs here rather than in the component. Every
 * setting is already carried by the data attributes the widget template renders, and
 * EntitySearchInput reads options in the order input, data attribute, default, so an empty object
 * is all this has to pass.
 */
export default class AutoEntitySearchInput {
  private readonly entitySearchInputs: EntitySearchInput[];

  constructor(selector: string = ComponentsMap.entitySearchInput.autoInitSelector) {
    this.entitySearchInputs = $(selector)
      .toArray()
      .map((container: HTMLElement) => new EntitySearchInput($(container), {}));
  }

  /**
   * The created components, in document order, for a page that needs to reach one of them after
   * automatic initialisation.
   */
  getEntitySearchInputs(): EntitySearchInput[] {
    return this.entitySearchInputs;
  }
}

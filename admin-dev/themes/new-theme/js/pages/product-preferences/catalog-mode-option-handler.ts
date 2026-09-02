/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

class CatalogModeOptionHandler {
  pageMap: Record<string, any>;

  constructor(pageMap: Record<string, any>) {
    this.pageMap = {
      catalogModeField: 'input[name="general[catalog_mode]"]',
      selectedCatalogModeField: 'input[name="general[catalog_mode]"]:checked',
      catalogModeOptions: '.catalog-mode-option',
      ...pageMap,
    };
    this.handle(0);

    $(this.pageMap.catalogModeField).on('change', () => this.handle(600));
  }

  handle(fadeLength: number): void {
    const catalogModeVal = $(this.pageMap.selectedCatalogModeField).val();
    const catalogModeEnabled = parseInt(<string>catalogModeVal, 10);

    const catalogOptions = $(this.pageMap.catalogModeOptions);

    if (catalogModeEnabled) {
      catalogOptions.show(fadeLength);
    } else {
      catalogOptions.hide(fadeLength / 2);
    }
  }
}

export default CatalogModeOptionHandler;

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Module Admin Page Loader.
 * @constructor
 */
class ModuleLoader {
  constructor() {
    ModuleLoader.handleImport();
  }

  static handleImport(): void {
    const moduleImport = $('#module-import');
    moduleImport.on('click', () => {
      // @ts-ignore
      moduleImport.addClass('onclick', 250, validate);
    });

    function validate() {
      setTimeout(() => {
        moduleImport.removeClass('onclick');
        // @ts-ignore
        moduleImport.addClass('validate', 450, callback);
      }, 2250);
    }
    function callback() {
      setTimeout(() => {
        moduleImport.removeClass('validate');
      }, 1250);
    }
  }
}

export default ModuleLoader;

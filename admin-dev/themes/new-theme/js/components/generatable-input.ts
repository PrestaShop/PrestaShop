/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Generates random values for inputs.
 *
 * Usage:
 *
 * There should be a button in HTML with 2 required data-* properties:
 *    1. data-target-input-id - input id for which value should be generated
 *    2. data-generated-value-size -
 *
 * Example button: <button class="js-generator-btn"
 *                         data-target-input-id="my-input-id"
 *                         data-generated-value-length="16"
 *                 >
 *                     Generate!
 *                 </button>
 *
 * In JavaScript you have to enable this functionality using GeneratableInput component like so:
 *
 * const generateableInput = new GeneratableInput();
 * generateableInput.attachOn('.js-generator-btn'); // every time our button is clicked
 *                                                  // it will generate random value of 16 characters
 *                                                  // for input with id of "my-input-id"
 *
 * Or if you use '.js-generator-btn' as default selector, you can just do:
 * new GeneratableInput();
 *
 * You can attach as many different buttons as you like using "attachOn()" function
 * as long as 2 required data-* attributes are present at each button.
 */
export default class GeneratableInput {
  /**
   * Constructor for GeneratableInput component
   * Attach event listeners on buttons that can generate random values, by default ".js-generator-btn".
   *
   * @param {String} generatorButtonsSelector
   */
  public constructor(generatorButtonsSelector?: string) {
    this.attachOn(generatorButtonsSelector ?? '.js-generator-btn');
  }

  /**
   * Attaches click event listeners on buttons than can generate random values
   *
   * @param {String} generatorButtonsSelector
   */
  public attachOn(generatorButtonsSelector: string): void {
    const generatorButtons = document.querySelectorAll(generatorButtonsSelector);

    generatorButtons.forEach((btn: Element): void => {
      btn.addEventListener('click', (event: Event): void => {
        const {attributes} = <HTMLButtonElement>event.currentTarget;

        const targetInputId = attributes.getNamedItem('data-target-input-id')
          ?.value;
        const generatedValueLength = parseInt(
          <string>attributes.getNamedItem('data-generated-value-length')?.value,
          10,
        );

        const targetInput = <HTMLInputElement>(
          document.querySelector(`#${targetInputId}`)
        );
        targetInput.value = this.generateValue(generatedValueLength);
        targetInput.dispatchEvent(new CustomEvent('change', {bubbles: true}));
      });
    });
  }

  /**
   * Generates random value for input
   *
   * @param {Number} length
   *
   * @returns {string}
   *
   * @private
   */
  private generateValue(length: number): string {
    const chars = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
    let generatedValue = '';

    for (let i = 1; i <= length; i += 1) {
      generatedValue += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    return generatedValue;
  }
}

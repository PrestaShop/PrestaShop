/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * A module supplies its update confirmation as plain text, while the confirm modal
 * renders its message as HTML, so the text has to be escaped on the way in.
 */
export const escapeHtml = (value: string): string => value
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;');

/**
 * Joins the core update advice with the messages the modules being updated supplied.
 * Empty parts are dropped so a module without a message changes nothing.
 */
export const buildUpdateConfirmMessage = (...parts: string[]): string => parts
  .filter((part: string) => part !== '')
  .join('<br><br>');

/**
 * Formats one module's message for a list covering several modules at once.
 */
export const formatModuleUpdateMessage = (
  moduleName: string,
  message: string,
): string => escapeHtml(`${moduleName}: ${message}`);

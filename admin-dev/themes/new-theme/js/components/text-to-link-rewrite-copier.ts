/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

interface TextToLinkParams {
  sourceElementSelector: string;
  destinationElementSelector: string;
  options?: Record<string, string>;
}

/**
 * Component which allows to copy regular text to url friendly text
 *
 * Usage example in template:
 *
 * <input name="source-input"
 *        class="js-link-rewrite-copier-source"> // The original text will be taken from this element
 * <input name="destination-input"
 *        class="js-link-rewrite-copier-destination"> // Modified text will be added to this input
 *
 * in javascript:
 *
 * textToLinkRewriteCopier({
 *   sourceElementSelector: '.js-link-rewrite-copier-source'
 *   destinationElementSelector: '.js-link-rewrite-copier-destination',
 * });
 *
 * If the source-input has value "test name" the link rewrite value will be "test-name".
 * If the source-input has value "test name #$" link rewrite will be "test-name-" since #$
 * are un allowed characters in url.
 *
 * You can also pass additional options to change the event name, or encoding format:
 *
 * textToLinkRewriteCopier({
 *   sourceElementSelector: '.js-link-rewrite-copier-source'
 *   destinationElementSelector: '.js-link-rewrite-copier-destination',
 *   options: {
 *     eventName: 'change', // default is 'input'
 *   }
 * });
 *
 */
const textToLinkRewriteCopier = ({
  sourceElementSelector,
  destinationElementSelector,
  options = {eventName: 'input'},
}: TextToLinkParams): void => {
  $(document).on(options.eventName, `${sourceElementSelector}`, (event) => {
    if (!$(event.currentTarget).closest('form').data('id')) {
      $(destinationElementSelector).val(
        window.str2url($(event.currentTarget).val(), 'UTF-8'),
      );
    }
  });
};

export default textToLinkRewriteCopier;

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import IframeEvent from '@components/modal/iframe-event';

/**
 * Client to integrate in a iframe in order to communicate with the parent window via some events.
 * The parent window needs to register to the IframeEvent:
 *
 * window.addEventListener(IframeEvent.parentWindowEvent, (event: IframeEvent) => {
 *   if (event.name === 'iframeAction') {
 *     doAction(event.parameters);
 *   }
 * });
 */
export default class IframeClient {
  private iframeWindow: Window;

  private parentWindow: Window;

  constructor() {
    this.iframeWindow = window;
    this.parentWindow = this.iframeWindow.parent;
  }

  dispatchEvent(eventName: string, parameters: any = {}): void {
    this.parentWindow.dispatchEvent(new IframeEvent(eventName, parameters));
  }
}

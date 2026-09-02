/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default class IframeEvent extends Event {
  static readonly parentWindowEvent: string = 'IframeClientEvent';

  private readonly eventName: string;

  private readonly eventParameters: any;

  constructor(eventName: string, parameters: any = {}) {
    super(IframeEvent.parentWindowEvent);
    this.eventName = eventName;
    this.eventParameters = parameters;
  }

  get name(): string {
    return this.eventName;
  }

  get parameters(): any {
    return this.eventParameters;
  }
}

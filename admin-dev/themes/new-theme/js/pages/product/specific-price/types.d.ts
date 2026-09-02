/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export interface SpecificPriceForListing {
  id: number,
  combination: string,
  currency: string,
  country: string,
  group: string,
  shop: string,
  customer: string,
  price: string,
  impact: string,
  period: Period|null,
  fromQuantity: string,
}

export interface Period {
  from: string,
  to: string
}

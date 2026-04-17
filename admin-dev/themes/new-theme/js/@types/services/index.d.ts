/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface PaginationServiceType {
  fetch: (offset: number, limit: number) => Promise<FetchResponse> | JQuery.jqXHR<any>;
}

export default PaginationServiceType;

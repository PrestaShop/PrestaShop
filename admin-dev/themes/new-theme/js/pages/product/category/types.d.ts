/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
export interface Category {
  id: number,
  name: string,
  displayName: string,
}

export interface TreeCategory {
  id: number,
  name: string,
  displayName: string,
  active: boolean,
  children: Array<TreeCategory>,
}

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {AutoCompleteSearchConfig} from '@components/auto-complete-search';
import PerfectScrollbar from '@node_modules/perfect-scrollbar';
// @ts-ignore
import Bloodhound from 'typeahead.js';

interface GroupedItemCollectionModalStates {
  itemGroups: Array<ItemGroup>,
  isModalShown: boolean,
  loading: boolean,
}

export interface GroupedItemsSelectorStates {
  dataSetConfig: AutoCompleteSearchConfig | Record<string, any>;
  searchSource: Bloodhound | null;
  scrollbar: PerfectScrollbar | null;
  searchableItems: Item[];
}

export interface ItemGroup {
  id: number;
  name: string;
  items: Array<Item>;
}

export interface Item {
  id: number;
  name: string;
  selected: boolean;
  groupId: number;
  groupName: string;
  color: string|null;
  texture: string|null;
}

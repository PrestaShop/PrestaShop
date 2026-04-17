/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {AutoCompleteSearchConfig} from '@js/components/auto-complete-search';
import PerfectScrollbar from 'perfect-scrollbar';

export interface AttributesSelectorStates {
  dataSetConfig: AutoCompleteSearchConfig | Record<string, any>;
  searchSource: Record<string, any>;
  scrollbar: PerfectScrollbar | null;
  hasGeneratedCombinations: boolean;
  checkboxList: Array<Record<string, any>>;
}

export interface AttributeGroup {
  id: number;
  name: string;
  publicName: string;
  attributes: Array<Attribute>;
}

/* eslint-disable camelcase */
export interface Attribute {
  id: number;
  color: string;
  group_id: number;
  group_name: string;
  name: string;
  texture: string|null;
}
/* eslint-enable camelcase */

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Shapes of the inline JSON payloads rendered by the form theme (frozen contracts — see the
 * catalog services under PrestaShop\Core\ExtraProperty\Catalog and ExtraPropertyConstraintCatalog).
 */

export interface FormCatalogEntry {
  id: string;
  label: string;
}

export interface GridColumnEntry {
  id: string;
  label: string;
  position: number;
}

export interface GridCatalogEntry {
  id: string;
  label: string;
  columns: GridColumnEntry[];
}

export interface ApiEndpointEntry {
  uriTemplate: string;
  methods: string[];
}

export interface ExtraPropertyCatalogs {
  forms: FormCatalogEntry[];
  grids: GridCatalogEntry[];
  apis: ApiEndpointEntry[];
  defaultFormTypes: Record<string, string>;
}

export interface ConstraintCatalogOption {
  type: string;
}

export interface ConstraintCatalogEntry {
  defaultOption: string | null;
  composite: boolean;
  required: string[];
  options: Record<string, ConstraintCatalogOption>;
}

export type ConstraintCatalog = Record<string, ConstraintCatalogEntry>;

/**
 * Reads an inline <script type="application/json"> payload, null when absent/malformed.
 */
export function readJsonPayload<T>(selector: string): T | null {
  const node = document.querySelector<HTMLScriptElement>(selector);

  if (!node) {
    return null;
  }

  try {
    return <T>JSON.parse(node.textContent ?? '');
  } catch (error) {
    console.error(`Could not parse the inline payload "${selector}"`, error);

    return null;
  }
}

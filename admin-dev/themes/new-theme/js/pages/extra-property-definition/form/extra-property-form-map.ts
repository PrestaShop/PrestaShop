/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Central selector map of the extra property definition form page (components-map.ts style):
 * every DOM hook of the row builders lives here, mirroring the data-role attributes rendered by
 * FormTheme/definition_form_theme.html.twig.
 */
export default {
  catalogsPayload: '[data-role="extra-property-catalogs"]',
  constraintCatalogPayload: '[data-role="extra-property-constraint-catalog"]',
  placementList: '[data-role="placement-list"]',
  constraintBuilder: '[data-role="constraint-builder"]',
  subsection: {
    rows: '[data-role="rows"]',
    row: '[data-role="row"]',
    builderView: '[data-role="builder-view"]',
    countBadge: '[data-role="count-badge"]',
    emptyState: '[data-role="empty-state"]',
    addRow: '[data-role="add-row"]',
    removeRow: '[data-role="remove-row"]',
    structuredFields: '[data-role="structured-fields"]',
    unknownPill: '[data-role="unknown-pill"]',
  },
  rowField: (field: string): string => `[name$="[${field}]"]`,
};

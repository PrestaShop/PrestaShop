/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';

/**
 * Clones one of the inert <template data-tpl="..."> skeletons rendered by the form theme (see
 * the extra_property_js_templates block). All dynamic markup of the page comes from these
 * templates — the JS only fills text/values and toggles attributes, in the same spirit as the
 * collection rows driven by the standard data-prototype.
 */
export default function cloneTemplate(name: string): HTMLElement {
  const template = document.querySelector<HTMLTemplateElement>(ExtraPropertyFormMap.template(name));
  const root = template?.content.firstElementChild;

  if (!root) {
    throw new Error(`Missing page template "${name}"`);
  }

  return <HTMLElement>root.cloneNode(true);
}

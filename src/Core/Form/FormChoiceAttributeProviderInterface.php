<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form;

/**
 * Interface FormChoiceProviderInterface defines contract for choice attribute providers.
 */
interface FormChoiceAttributeProviderInterface
{
    /**
     * Get choices attributes.
     *  return [{choice_value} => [...$attributes]]
     *
     * @return array
     */
    public function getChoicesAttributes();
}

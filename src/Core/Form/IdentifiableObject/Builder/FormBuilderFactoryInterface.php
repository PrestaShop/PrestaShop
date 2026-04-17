<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

/**
 * Defines interface for creating form builders.
 *
 * @todo in next major: Add ?FormOptionProviderInterface $optionProvider = null as a third parameter to this interface
 */
interface FormBuilderFactoryInterface
{
    /**
     * @param string $formType
     * @param FormDataProviderInterface $dataProvider
     *
     * @return FormBuilderInterface
     */
    public function create($formType, FormDataProviderInterface $dataProvider);
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\RequiredFields;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides choices for configuring required fields for address
 */
final class AddressRequiredFieldsChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return array_combine(RequiredFields::ALLOWED_REQUIRED_FIELDS, RequiredFields::ALLOWED_REQUIRED_FIELDS);
    }
}

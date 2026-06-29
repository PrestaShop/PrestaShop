<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides human-readable choices for ExtraPropertyType, shared by the extra property
 * definition form and grid filter so the translated labels are not duplicated.
 */
final class ExtraPropertyTypeChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        return [
            $this->translator->trans('Integer', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::INT->value,
            $this->translator->trans('Boolean', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::BOOL->value,
            $this->translator->trans('Text', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::STRING->value,
            $this->translator->trans('Decimal number', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::FLOAT->value,
            $this->translator->trans('Date', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::DATE->value,
            $this->translator->trans('Rich text (HTML)', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::HTML->value,
            $this->translator->trans('JSON', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::JSON->value,
            $this->translator->trans('Choice list', [], 'Admin.Advparameters.Feature') => ExtraPropertyType::CHOICE->value,
        ];
    }
}

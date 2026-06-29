<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides human-readable choices for ExtraPropertyScope, shared by the extra property
 * definition form and grid filter so the translated labels are not duplicated.
 */
final class ExtraPropertyScopeChoiceProvider implements FormChoiceProviderInterface
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
            $this->translator->trans('Common (one value per entity)', [], 'Admin.Advparameters.Feature') => ExtraPropertyScope::COMMON->value,
            $this->translator->trans('Per language', [], 'Admin.Advparameters.Feature') => ExtraPropertyScope::LANG->value,
            $this->translator->trans('Per shop', [], 'Admin.Advparameters.Feature') => ExtraPropertyScope::SHOP->value,
        ];
    }
}

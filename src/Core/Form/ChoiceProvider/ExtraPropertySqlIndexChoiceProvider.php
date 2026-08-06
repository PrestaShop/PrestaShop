<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides human-readable choices for ExtraPropertySqlIndex, shared by the extra property
 * definition form and (potentially) grid filters so the translated labels are not duplicated.
 */
final class ExtraPropertySqlIndexChoiceProvider implements FormChoiceProviderInterface
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
            $this->translator->trans('No index', [], 'Admin.Advparameters.Feature') => ExtraPropertySqlIndex::NONE->value,
            $this->translator->trans('Standard index', [], 'Admin.Advparameters.Feature') => ExtraPropertySqlIndex::KEY->value,
            $this->translator->trans('Unique index', [], 'Admin.Advparameters.Feature') => ExtraPropertySqlIndex::UNIQUE->value,
        ];
    }
}

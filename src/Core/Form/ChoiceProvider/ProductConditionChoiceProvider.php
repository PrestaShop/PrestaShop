<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductConditionChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        return [
            $this->translator->trans('New', [], 'Admin.Catalog.Feature') => ProductCondition::NEW,
            $this->translator->trans('New, open box', [], 'Admin.Catalog.Feature') => ProductCondition::OPEN_BOX,
            $this->translator->trans('New, minor defects', [], 'Admin.Catalog.Feature') => ProductCondition::NEW_WITH_DEFECTS,
            $this->translator->trans('Used', [], 'Admin.Catalog.Feature') => ProductCondition::USED,
            $this->translator->trans('Refurbished', [], 'Admin.Catalog.Feature') => ProductCondition::REFURBISHED,
            $this->translator->trans('Damaged', [], 'Admin.Catalog.Feature') => ProductCondition::DAMAGED,
        ];
    }
}

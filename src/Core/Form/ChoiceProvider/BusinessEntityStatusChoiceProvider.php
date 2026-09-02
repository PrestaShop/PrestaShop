<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides the business entity status choices for the grid filter. Labels come from
 * BusinessEntityStatus itself, so the filter, the grid rows and the detail view all display the
 * same wording.
 */
final class BusinessEntityStatusChoiceProvider implements FormChoiceProviderInterface
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
        $choices = [];
        foreach (BusinessEntityStatus::cases() as $status) {
            $choices[$status->trans($this->translator)] = $status->value;
        }

        return $choices;
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpecificPricePriorityChoiceProvider implements FormChoiceProviderInterface
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
     * @return array<string, string>
     */
    public function getChoices(): array
    {
        return [
            $this->translator->trans('Store', [], 'Admin.Global') => PriorityList::PRIORITY_SHOP,
            $this->translator->trans('Currency', [], 'Admin.Global') => PriorityList::PRIORITY_CURRENCY,
            $this->translator->trans('Country', [], 'Admin.Global') => PriorityList::PRIORITY_COUNTRY,
            $this->translator->trans('Group', [], 'Admin.Global') => PriorityList::PRIORITY_GROUP,
        ];
    }
}

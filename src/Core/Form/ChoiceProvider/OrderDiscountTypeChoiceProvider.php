<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Order\OrderDiscountType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderDiscountTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return [
            $this->translator->trans('Percent', [], 'Admin.Global') => OrderDiscountType::DISCOUNT_PERCENT,
            $this->translator->trans('Amount', [], 'Admin.Global') => OrderDiscountType::DISCOUNT_AMOUNT,
            $this->translator->trans('Free shipping', [], 'Admin.Shipping.Feature') => OrderDiscountType::FREE_SHIPPING,
        ];
    }
}

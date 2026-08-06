<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class DiscountTypeChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        protected readonly DiscountTypeRepository $repository,
        protected readonly LanguageContext $languageContext,
    ) {
    }

    public function getChoices()
    {
        $discountTypes = $this->repository->getAllActiveTypes($this->languageContext->getId());
        $discountTypes = array_filter($discountTypes, static function (array $discountType) {
            // Order level type is hidden for now
            return $discountType['discount_type'] !== DiscountType::ORDER_LEVEL;
        });

        return FormChoiceFormatter::formatFormChoices(
            $discountTypes,
            'id_cart_rule_type',
            'name'
        );
    }
}

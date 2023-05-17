<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\CartRule;

use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\CartRule\CartRuleActionBuilder;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

abstract class AbstractCartRuleFeatureContext extends AbstractDomainFeatureContext
{
    protected function getCartRuleActionBuilder(): CartRuleActionBuilder
    {
        return new CartRuleActionBuilder();
    }

    /**
     * This method reformats the data from the behat feature files into an array structure understandable by the CartRuleActionBuilder (based on the form structure).
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function formatDataForActionBuilder(array $data): array
    {
        $formattedData = [];

        if (isset($data['free_shipping'])) {
            $formattedData['free_shipping'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']);
        }

        if (isset($data['discount_percentage'])) {
            $formattedData['discount']['reduction']['value'] = $data['discount_percentage'];
            $formattedData['discount']['reduction']['type'] = Reduction::TYPE_PERCENTAGE;
            $formattedData['discount']['apply_to_discounted_products'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['apply_to_discounted_products']);
        }

        if (isset($data['discount_amount'])) {
            $formattedData['discount']['reduction'] = [
                'value' => $data['discount_amount'],
                'type' => Reduction::TYPE_AMOUNT,
                'currency' => $this->getSharedStorage()->get($data['discount_currency']),
                'include_tax' => PrimitiveUtils::castStringBooleanIntoBoolean($data['discount_includes_tax']),
            ];
        }

        if (isset($data['discount_application_type'])) {
            $formattedData['discount']['discount_application'] = $data['discount_application_type'];
        }

        if (isset($data['discount_product'])) {
            $formattedData['discount']['specific_product'][0]['id'] = (int) $this->getSharedStorage()->get($data['discount_product']);
        }

        if (isset($data['gift_product'])) {
            $formattedData['gift_product'][0]['product_id'] = (int) $this->getSharedStorage()->get($data['gift_product']);
            if (isset($data['gift_combination'])) {
                $formattedData['gift_product'][0]['combination_id'] = $this->getSharedStorage()->get($data['gift_combination']);
            }
        }

        return $formattedData;
    }
}

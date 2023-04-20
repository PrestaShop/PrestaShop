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

use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\CartRule\CartRuleActionBuilder;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

abstract class AbstractCartRuleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @param array<string, mixed> $data
     *
     * @return CartRuleActionInterface
     */
    protected function buildCartRuleAction(array $data): CartRuleActionInterface
    {
        //@todo: how it should behave for edition?
        // The edition command doesn't require changing the action, even though in form layer we will probably always provide all the same values.
        // need to think about this more.
        return (new CartRuleActionBuilder())->build($this->formatDataForActionBuilder($data));
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function formatDataForActionBuilder(array $data): array
    {
        $formattedData = [
        ];

        if (isset($data['free_shipping'])) {
            $formattedData['free_shipping'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']);
        }

        if (isset($data['reduction_percentage'])) {
            $formattedData['discount']['reduction']['value'] = $data['reduction_percentage'];
            $formattedData['discount']['reduction']['type'] = Reduction::TYPE_PERCENTAGE;
            $formattedData['discount']['apply_to_discounted_products'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_apply_to_discounted_products']);
        }

        if (isset($data['reduction_amount'])) {
            $formattedData['discount']['reduction']['value'] = $data['reduction_amount'];
            $formattedData['discount']['reduction']['type'] = Reduction::TYPE_AMOUNT;
            $formattedData['currency'] = $this->getSharedStorage()->get($data['reduction_currency']);
            $formattedData['discount']['reduction']['include_tax'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['reduction_tax']);
        }

        if (isset($data['discount_application_type'])) {
            $formattedData['discount']['discount_application'] = $data['discount_application_type'];
        }

        if (isset($data['discount_product'])) {
            $formattedData['discount']['specific_product'] = (int) $data['discount_product'];
        }

        if (isset($data['gift_product_id'])) {
            $formattedData['gift_product'][0] = (int) $data['gift_product_id'];

            if (isset($data['gift_product_attribute_id'])) {
                $formattedData['gift_product'][0]['combination_id'] = (int) $data['gift_product_attribute_id'];
            }
        }

        return $formattedData;
    }
}

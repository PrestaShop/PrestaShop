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

namespace PrestaShop\PrestaShop\Adapter\Discount\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountTypes;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryHandler\GetDiscountTypesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountTypeList;

#[AsQueryHandler]
class GetDiscountTypesHandler implements GetDiscountTypesHandlerInterface
{
    public function __construct(
        private readonly DiscountTypeRepository $discountTypeRepository,
    ) {
    }

    /**
     * @return DiscountTypeList[]
     */
    public function handle(GetDiscountTypes $query): array
    {
        $allTypes = $this->discountTypeRepository->getAllTypes();

        // Group by id_cart_rule_type to collect all translations
        $groupedTypes = [];
        foreach ($allTypes as $type) {
            $idCartRuleType = (int) $type['id_cart_rule_type'];
            if (!isset($groupedTypes[$idCartRuleType])) {
                $groupedTypes[$idCartRuleType] = [
                    'id_cart_rule_type' => $idCartRuleType,
                    'discount_type' => $type['discount_type'],
                    'is_core' => (bool) $type['is_core'],
                    'active' => (bool) $type['active'],
                    'names' => [],
                    'descriptions' => [],
                ];
            }

            // Add translation if available, using language ID as key
            if (!empty($type['id_lang']) && !empty($type['name'])) {
                $groupedTypes[$idCartRuleType]['names'][(string) $type['id_lang']] = $type['name'];
            }
            if (!empty($type['id_lang']) && !empty($type['description'])) {
                $groupedTypes[$idCartRuleType]['descriptions'][(string) $type['id_lang']] = $type['description'];
            }
        }

        $discountTypeSummaries = [];
        foreach ($groupedTypes as $type) {
            $discountTypeSummaries[] = new DiscountTypeList(
                $type['id_cart_rule_type'],
                $type['discount_type'],
                $type['names'],
                $type['descriptions'],
                $type['is_core'],
                $type['active']
            );
        }

        return $discountTypeSummaries;
    }
}

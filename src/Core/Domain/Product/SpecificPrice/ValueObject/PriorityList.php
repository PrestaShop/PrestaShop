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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceConstraintException;

/**
 * Holds valid specific price priority values
 */
class PriorityList
{
    public const PRIORITY_COUNTRY = 'id_country';
    public const PRIORITY_CURRENCY = 'id_currency';
    public const PRIORITY_GROUP = 'id_group';
    public const PRIORITY_SHOP = 'id_shop';

    public const AVAILABLE_PRIORITIES = [
        'country' => self::PRIORITY_COUNTRY,
        'currency' => self::PRIORITY_CURRENCY,
        'group' => self::PRIORITY_GROUP,
        'shop' => self::PRIORITY_SHOP,
    ];

    /**
     * @var string[]
     */
    private $priorities;

    /**
     * @param string[] $priorities
     */
    public function __construct(array $priorities)
    {
        $this->assertPriorities($priorities);
        $this->priorities = $priorities;
    }

    /**
     * @return string[]
     */
    public function getPriorities(): array
    {
        return $this->priorities;
    }

    /**
     * @param string[] $priorities
     *
     * @throws SpecificPriceConstraintException
     */
    private function assertPriorities(array $priorities): void
    {
        $checkedPriorities = [];
        foreach ($priorities as $priority) {
            if (!in_array($priority, static::AVAILABLE_PRIORITIES, true)) {
                throw new SpecificPriceConstraintException(
                    sprintf('Invalid priority value "%s"', $priority),
                    SpecificPriceConstraintException::INVALID_PRIORITY
                );
            }

            if (in_array($priority, $checkedPriorities)) {
                throw new SpecificPriceConstraintException(
                    'Invalid priorities. Priorities cannot duplicate.',
                    SpecificPriceConstraintException::DUPLICATE_PRIORITY
                );
            }

            $checkedPriorities[] = $priority;
        }
    }
}

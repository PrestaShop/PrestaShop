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

namespace PrestaShop\PrestaShop\Core\Domain\Store\Command;

use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

/**
 * Toggles store status on bulk action
 */
class BulkUpdateStoreStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var array<int, StoreId>
     */
    private $storeIds;

    /**
     * @param bool $expectedStatus
     * @param array<int, int> $storeIds
     */
    public function __construct(bool $expectedStatus, array $storeIds)
    {
        $this->setStoreIds($storeIds);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return array<int, StoreId>
     */
    public function getStoreIds(): array
    {
        return $this->storeIds;
    }

    /**
     * @param array<int, int> $storeIds
     */
    private function setStoreIds(array $storeIds): void
    {
        foreach ($storeIds as $storeId) {
            $this->storeIds[] = new StoreId((int) $storeId);
        }
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }
}

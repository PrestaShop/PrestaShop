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

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;

/**
 * Defines Search Engine ID with it's constraints.
 */
class SearchEngineId
{
    /**
     * @var int
     */
    private $searchEngineId;

    /**
     * @param int $searchEngineId
     *
     * @throws SearchEngineException
     */
    public function __construct(int $searchEngineId)
    {
        $this->assertIntegerIsGreaterThanZero($searchEngineId);
        $this->searchEngineId = $searchEngineId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->searchEngineId;
    }

    /**
     * @param int $searchEngineId
     *
     * @throws SearchEngineException
     */
    private function assertIntegerIsGreaterThanZero(int $searchEngineId): void
    {
        if (0 >= $searchEngineId) {
            throw new SearchEngineException(sprintf('Search engine id %d is invalid. Search engine id have to be number bigger than zero.', $searchEngineId));
        }
    }
}

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

namespace PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;

/**
 * Defines Feature ID with its constraints.
 */
class FeatureId
{
    /**
     * @var int
     */
    private $featureId;

    /**
     * @param int $featureId
     */
    public function __construct(int $featureId)
    {
        $this->assertIsGreaterThanZero($featureId);
        $this->featureId = $featureId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->featureId;
    }

    /**
     * @param int $featureId
     *
     * @throws FeatureConstraintException
     */
    private function assertIsGreaterThanZero(int $featureId): void
    {
        if (0 >= $featureId) {
            throw new FeatureConstraintException(
                sprintf('Invalid feature id %d. It must be greater than zero.', $featureId),
                FeatureConstraintException::INVALID_ID
            );
        }
    }
}
